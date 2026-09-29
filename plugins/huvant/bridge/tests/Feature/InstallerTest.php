<?php

use Huvant\Bridge\BridgeServiceProvider;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Package;

require_once __DIR__.'/../../../../webkul/support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    foreach (['employees', 'projects', 'timesheets'] as $dependency) {
        TestBootstrapHelper::ensurePluginInstalled($dependency);
    }
});

it('does not reinstall dependencies that are already installed', function () {
    $package = new Package;
    (new BridgeServiceProvider(app()))->configureCustomPackage($package);

    $configuredCommand = collect($package->consoleCommands)
        ->first(fn (object $command): bool => $command instanceof InstallCommand);
    $command = new class($package) extends InstallCommand
    {
        public array $calledCommands = [];

        public function call($command, array $arguments = [])
        {
            $this->calledCommands[] = $command;

            return 0;
        }
    };

    ($configuredCommand->startWith)($command);

    expect($command->calledCommands)->toBeEmpty()
        ->and($package->dependencies)->toBe(['employees', 'projects', 'timesheets']);
});

it('installs a missing dependency while retaining dependency metadata', function () {
    $package = new Package;
    (new BridgeServiceProvider(app()))->configureCustomPackage($package);

    Package::isPluginInstalled('employees');
    $timesheets = Package::$plugins->pull('timesheets');

    try {
        $configuredCommand = collect($package->consoleCommands)
            ->first(fn (object $command): bool => $command instanceof InstallCommand);
        $command = new class($package) extends InstallCommand
        {
            public array $calledCommands = [];

            public function call($command, array $arguments = [])
            {
                $this->calledCommands[] = $command;

                return 0;
            }
        };

        ($configuredCommand->startWith)($command);

        expect($command->calledCommands)->toBe(['timesheets:install'])
            ->and($package->dependencies)->toBe(['employees', 'projects', 'timesheets']);
    } finally {
        Package::$plugins->put('timesheets', $timesheets);
    }
});
