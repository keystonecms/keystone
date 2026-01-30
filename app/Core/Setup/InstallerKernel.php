<?php

namespace Keystone\Core\Setup;

use Keystone\Core\Setup\Step\InstallerStepInterface;
use Keystone\Core\Setup\InstallerState;


final class InstallerKernel {

    /** @var InstallerStepInterface[] */
    private array $steps = [];

    private InstallerState $state;

    public function __construct(iterable $steps) {
        foreach ($steps as $step) {
            $this->steps[] = $step;
        }

        $this->state = $_SESSION['installer_state'] ?? new InstallerState();
       }

public function getStepByIndex(int $index): ?InstallerStepInterface {
    return $this->steps[$index - 1] ?? null;
}


private function nextStepAfter(InstallerStepInterface $currentStep): ?string {
    $found = false;

    foreach ($this->steps as $step) {
        if ($found) {
            return $step->getName();
        }

        if ($step === $currentStep) {
            $found = true;
        }
    }

    return null; // geen volgende step = klaar
}


    public function run(): void {

        foreach ($this->steps as $step) {
            if (! $step->shouldRun($this->state)) {
                continue;
            }

            $this->runStep($step);
        }
    }

    public function runUntil(string $stepName): void {

            if (
                $step->getName() === 'finalize'
                && $this->state->installationFinalized === true
            ) {
                $this->completionService->complete($this->state);
            }

        foreach ($this->steps as $step) {
            if (! $step->shouldRun($this->state)) {
                continue;
            }

            $this->runStep($step);

            if ($step->getName() === $stepName) {
                break;
            }
        }
    }

public function runStep(string $stepName, array $payload = []): array {

    // 1. State vullen
    $this->state->hydrate($payload);

    // 2. Juiste step zoeken
    foreach ($this->steps as $step) {
        if ($step->getName() !== $stepName) {
            continue;
        }



        if (!$step->shouldRun($this->state)) {
            throw new InstallerException(['Step cannot run in current state']);
        }

        // 3. Step uitvoeren
        $step->run($this->state);

        $_SESSION['installer_state'] = $this->state;

        $index = $this->stepIndex($step);
        $total = count($this->steps);


        return [
            'nextStep' => $this->nextStepAfter($step),
            'messages' => [$step->getName() . ' completed'],

            'meta' => [
                        'step' => $step->getName(),
                        'title' => $step->getTitle(),
                        'description' => $step->getDescription(),
                ],
            'progress' => [
                'current' => $index + 1,
                'total'   => $total,
                'percent' => (int) round((($index + 1) / $total) * 100),
            ],
        ];
    }

    throw new InstallerException(['Unknown step: ' . $stepName]);
    }

private function stepIndex(InstallerStepInterface $step): int {
    foreach ($this->steps as $i => $s) {
        if ($s === $step) {
            return $i;
        }
    }
    return 0;
    }
}


?>