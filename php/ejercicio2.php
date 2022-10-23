<?php

// EJERCICIO 2

class Group
{
    const WINNER = 3;
    const DRAW = 1;
    const LOSE = 0;

    private array $teams;

    private array $matches;

    private array $positionsTable;

    public function __construct(array $teams)
    {
        if (count($teams) !== 4) {
            throw new Exception('Must enter precisely four team names.');
        }

        foreach ($teams as $team) {
            if ('' === $team || !is_string($team)) {
                throw new Exception('Must enter valid names for your teams.');
            }
        }

        $this->teams = $teams;
        $this->matches = [];
        $this->positionsTable = array_fill_keys($teams, [
            'points' => 0,
            'matchesWin' => 0,
            'matchesDraw' => 0,
            'matchesLose' => 0,
            'goalsFor' => 0,
            'goalsAgainst' => 0,
            'goalDifference' => 0,
        ]);
    }

    public function match(string $team1, int $score1, string $team2, int $score2)
    {
        if (!in_array($team1, $this->teams, true)) {
            throw new Exception('Must enter a correct name for team1.');
        }
        if (!in_array($team1, $this->teams, true)) {
            throw new Exception('Must enter a correct name for team2.');
        }
        if ($this->isDuplicate($team1, $team2)) {
            throw new Exception('This match is duplicated.');
        }

        $match = [
            'team1' => $team1,
            'score1' => $score1,
            'team2' => $team2,
            'score2' => $score2,
        ];
        $this->matches[] = $match;
        self::matchPoints($this->positionsTable, $match);
        self::orderPositionsTable($this->positionsTable);
    }

    public function result(): array
    {
        return $this->positionsTable;
        // return array_keys($this->positionsTable);
    }

    /**
     * Assign match points into positions table.
     */
    private static function matchPoints(&$positionsTable, array $match)
    {
        if ($match['score1'] > $match['score2']) {
            self::updatePositionsTable($positionsTable[$match['team1']], self::WINNER, $match['score1'], $match['score2']);
            self::updatePositionsTable($positionsTable[$match['team2']], self::LOSE, $match['score2'], $match['score1']);
        } elseif ($match['score1'] < $match['score2']) {
            self::updatePositionsTable($positionsTable[$match['team1']], self::LOSE, $match['score1'], $match['score2']);
            self::updatePositionsTable($positionsTable[$match['team2']], self::WINNER, $match['score2'], $match['score1']);
        } else {
            self::updatePositionsTable($positionsTable[$match['team1']], self::DRAW, $match['score1'], $match['score2']);
            self::updatePositionsTable($positionsTable[$match['team2']], self::DRAW, $match['score2'], $match['score1']);
        }
    }

    private static function updatePositionsTable(&$positionsTable, $points, $for, $against)
    {
        switch($points) {
            case self::WINNER:
                $positionsTable['matchesWin']++;
                break;
            case self::DRAW:
                $positionsTable['matchesDraw']++;
                break;
            case self::LOSE:
                $positionsTable['matchesLose']++;
                break;
        }

        $positionsTable['points'] += $points;
        $positionsTable['goalsFor'] += $for;
        $positionsTable['goalsAgainst'] += $against;
        $positionsTable['goalDifference'] = $positionsTable['goalsFor'] - $positionsTable['goalsAgainst'];
    }

    private function orderPositionsTable(&$positionsTable): void
    {
        uksort($positionsTable, function ($a, $b) use (&$positionsTable) {
            $teamA = $positionsTable[$a];
            $teamB = $positionsTable[$b];

            return self::tiebraker($teamA, $teamB);
        });
    }

    private function orderTiedPositionsTable(): void
    {
        $tiedTeams = [];
        foreach ($this->teams as $teamA) {
            foreach ($this->teams as $teamB) {
                if ($teamA === $teamB) {
                    continue;
                }

                if ($this->positionsTable[$teamA]['points'] === $this->positionsTable[$teamB]['points']) {
                    if (!in_array($teamA, $tiedTeams, true)) {
                        $tiedTeams[] = $teamA;
                    }
                    if (!in_array($teamB, $tiedTeams, true)) {
                        $tiedTeams[] = $teamB;
                    }
                }
            }

            if (count($tiedTeams) === 4) {
                break;
            }
        }

        // case (c)
        $tiedPositionsTable = array_fill_keys($tiedTeams, [
            'points' => 0,
            'matchesWin' => 0,
            'matchesDraw' => 0,
            'matchesLose' => 0,
            'goalsFor' => 0,
            'goalsAgainst' => 0,
            'goalDifference' => 0,
        ]);
        foreach ($this->matches as $match) {
            if (in_array($match['team1'], $tiedTeams, true) && in_array($match['team2'], $tiedTeams, true)) {
                self::matchPoints($subPositionsTable, $match);
            }
        }
        self::orderPositionsTable($tiedPositionsTable);
    }

    private static function tiebraker(array $teamA, array $teamB): int
    {
        // b.i
        if ($teamA['points'] !== $teamB['points']) {
            return ($teamA['points'] > $teamB['points']) ? -1 : 1;
        }
        // b.ii
        if ($teamA['goalDifference'] !== $teamB['goalDifference']) {
            return ($teamA['goalDifference'] > $teamB['goalDifference']) ? -1 : 1;
        }
        // b.iii
        if ($teamA['goalsFor'] !== $teamB['goalsFor']) {
            return ($teamA['goalsFor'] > $teamB['goalsFor']) ? -1 : 1;
        }

        return 0;
    }

    /**
     * Check whether or not it's a duplicate match.
     */
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

$groupA = new Group(['Brasil', 'Colombia', 'Argentina', 'Uruguay']);
$groupA->match('Argentina', 1, 'Brasil', 1);
$groupA->match('Argentina', 3, 'Colombia', 0);
$groupA->match('Argentina', 2, 'Uruguay', 1);
$groupA->match('Brasil', 3, 'Colombia', 1);
$groupA->match('Brasil', 2, 'Uruguay', 0);
$groupA->match('Colombia', 3, 'Uruguay', 1);

/* 
| Equipo    | Ganados | Empate | Perdidos | A Favor | En Contra | Diferencia | Puntos |
| Brasil    | 2       | 1      | 0        | 6       | 2         | +4         | 7      |
| Argentina | 2       | 1      | 0        | 6       | 2         | +4         | 7      |
| Colombia  | 1       | 0      | 2        | 4       | 7         | -4         | 3      |
| Uruguay   | 0       | 1      | 2        | 3       | 7         | -4         | 1      |
*/

print_r($groupA->result());
