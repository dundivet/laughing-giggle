<?php

// EJERCICIO 2

class Group
{
    private array $teams;

    private array $matches;

    public function __construct(array $teams)
    {
        if (!count($teams) !== 4) {
            throw new Exception('Must enter precisely four team names.');
        }

        foreach ($teams as $team) {
            if ('' === $team || !is_string($team)) {
                throw new Exception('Must enter valid names for your teams.');
            }
        }

        $this->teams = $teams;
    }

    public function match(string $team1, int $score1, string $team2, int $score2)
    {
        if (false === array_search($team1, $this->teams, true)) {
            throw new Exception('Must enter a correct name for team1.');
        }
        if (false === array_search($team1, $this->teams, true)) {
            throw new Exception('Must enter a correct name for team2.');
        }
        if ($this->isDuplicate($team1, $team2)) {
            throw new Exception('This match is duplicated.');
        }

        $this->matches[] = [
            'team1' => $team1,
            'team2' => $team2,
            'score1' => $score1,
            'score2' => $score2
        ];
    }

    public function result(): array
    {
        
    }

    private function isDuplicate(string $team1, string $team2): bool
    {
        $filtered = array_filter($this->matches, function ($row) use ($team1, $team2) {
            return ($row['team1'] === $team1 || $row['team2'] === $team1) && ($row['team1'] === $team2 || $row['team2'] === $team2);
        });

        if (count($filtered) > 0) {
            return true;
        }
        
        return false;
    }
}