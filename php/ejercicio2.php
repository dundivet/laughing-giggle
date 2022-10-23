<?php

// EJERCICIO 2

class Group
{
    const WINNER = 3;
    const DRAW = 1;
    const LOSE = 0;

    const DEFAULT_POSITION_TABLE = [
        'points' => 0,
        'matchesWin' => 0,
        'matchesDraw' => 0,
        'matchesLose' => 0,
        'matchesPlayed' => 0,
        'goalsFor' => 0,
        'goalsAgainst' => 0,
        'goalDifference' => 0,
    ];

    private array $teams;

    private array $matches;

    private array $positionsTable;

    private bool $tied;

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
        $this->tied = false;
        $this->positionsTable = array_fill_keys($teams, self::DEFAULT_POSITION_TABLE);
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
        $this->tied = false;

        self::matchPoints($this->positionsTable, $match);
        self::orderPositionsTable($this->positionsTable);
        if ($this->tied) {
            self::orderTiedPositionsTable($this->positionsTable);
        }
    }

    public function result(): array
    {
        // return $this->positionsTable;
        return array_keys($this->positionsTable);
    }

    /**
     * Assign match points into positions table.
     */
    private static function matchPoints(&$positionsTable, array $match)
    {
        if (!in_array($match['team1'], array_keys($positionsTable))) {
            $positionsTable[$match['team1']] = self::DEFAULT_POSITION_TABLE;
        }
        if (!in_array($match['team2'], array_keys($positionsTable))) {
            $positionsTable[$match['team2']] = self::DEFAULT_POSITION_TABLE;
        }

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

        $positionsTable['matchesPlayed']++;
        $positionsTable['points'] += $points;
        $positionsTable['goalsFor'] += $for;
        $positionsTable['goalsAgainst'] += $against;
        $positionsTable['goalDifference'] = $positionsTable['goalsFor'] - $positionsTable['goalsAgainst'];
    }

    private function orderPositionsTable(array &$positionsTable, bool $global = true): void
    {
        uksort($positionsTable, function ($a, $b) use (&$positionsTable, $global) {
            $teamA = $positionsTable[$a];
            $teamB = $positionsTable[$b];

            $result = self::tiebraker($teamA, $teamB);
            if ($result === 0 && $global && $teamA['matchesPlayed'] > 0 && $teamB['matchesPlayed'] > 0) {
                $this->tied = true;
            }

            return $result;
        });
    }

    private function orderTiedPositionsTable(&$positionsTable): void
    {
        // find tied teams
        $tiedTeams = [];
        $auxCounter = [];
        foreach ($positionsTable as $team => $row) {
            $auxCounter[$row['points']][] = $team;
            if (count($auxCounter[$row['points']]) > 1) {
                $tiedTeams = $auxCounter[$row['points']];
            }
        }

        // case (c)
        // find positions table with tied teams only
        $tiedPositionsTable = array_fill_keys($tiedTeams, self::DEFAULT_POSITION_TABLE);
        foreach ($this->matches as $match) {
            if (in_array($match['team1'], $tiedTeams, true) && in_array($match['team2'], $tiedTeams, true)) {
                self::matchPoints($tiedPositionsTable, $match);
            }
        }
        self::orderPositionsTable($tiedPositionsTable, false);

        // reordering with tied positions table
        $auxPositionsTable = $positionsTable;
        $positionsTable = [];
        $tieflag = false;
        foreach ($auxPositionsTable as $team => $row) {
            if (in_array($team, $tiedTeams)) {
                $tieflag = true;
                continue;
            }

            if ($tieflag) {
                foreach ($tiedPositionsTable as $teamT => $rowT) {
                    $positionsTable[$teamT] = $auxPositionsTable[$teamT];
                }
                $tieflag = false;
            }

            $positionsTable[$team] = $row;
        }
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

$groupA = new Group(['Colombia', 'Japón', 'Senegal', 'Polonia']);
$groupA->match('Senegal', 0, 'Colombia', 1);
$groupA->match('Japón', 0, 'Polonia', 1);
$groupA->match('Senegal', 2, 'Japón', 2);
$groupA->match('Polonia', 0, 'Colombia', 3);
$groupA->match('Polonia', 1, 'Senegal', 2);
$groupA->match('Colombia', 1, 'Japón', 3);

print_r($groupA->result());

// $groupB = new Group(['Brasil', 'Colombia', 'Argentina', 'Uruguay']);
// $groupB->match('Argentina', 1, 'Brasil', 1);
// $groupB->match('Argentina', 2, 'Colombia', 0);
// $groupB->match('Brasil', 2, 'Colombia', 0);
// $groupB->match('Colombia', 3, 'Uruguay', 1);
// $groupB->match('Argentina', 2, 'Uruguay', 1);
// $groupB->match('Brasil', 2, 'Uruguay', 0);

// /* 
// | Equipo    | Ganados | Empate | Perdidos | A Favor | En Contra | Diferencia | Puntos |
// | Brasil    | 2       | 1      | 0        | 5       | 1         | +4         | 7      |
// | Argentina | 2       | 1      | 0        | 5       | 2         | +3         | 7      |
// | Colombia  | 1       | 0      | 2        | 3       | 5         | -2         | 3      |
// | Uruguay   | 0       | 0      | 3        | 2       | 7         | -5         | 0      |
// */

// print_r($groupB->result());
