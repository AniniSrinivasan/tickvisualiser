<?php
require_once __DIR__ . '/db_connect.php';

function getDashboardSearchColumns(): array
{
    return [
        'location_name' => [
            'table' => 'l',
            'column' => 'location_name'
        ],
        'species_name' => [
            'table' => 'sp',
            'column' => 'species_name'
        ],
        'species_latin_name' => [
            'table' => 'sp',
            'column' => 'species_latin_name'
        ]
    ];
}

function parseDashboardSearch(string $search): array
{
    $search = trim($search);

    if ($search === '') {
        return [
            'mode' => 'empty',
            'column' => null,
            'value' => '',
            'clauses' => [],
            'operators' => []
        ];
    }

    $columns = getDashboardSearchColumns();
    preg_match_all('/@([a-zA-Z_][a-zA-Z0-9_]*)\s*:\s*([^@]+?)(?=\s+(?:AND|OR)\s+@|$)/i', $search, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);

    $clauses = [];
    $operators = [];
    $previousEnd = 0;
    $validStructuredSearch = true;

    foreach ($matches as $index => $match) {
        $fullMatch = $match[0][0];
        $fullOffset = $match[0][1];
        $column = trim($match[1][0]);
        $value = trim($match[2][0]);

        if (!isset($columns[$column]) || $value === '') {
            $validStructuredSearch = false;
            continue;
        }

        $between = trim(substr($search, $previousEnd, $fullOffset - $previousEnd));

        if ($index === 0) {
            if ($between !== '') {
                $validStructuredSearch = false;
            }
        } else {
            if (!preg_match('/^(AND|OR)$/i', $between, $operatorMatch)) {
                $validStructuredSearch = false;
                continue;
            }

            $operators[] = strtoupper($operatorMatch[1]);
        }

        $clauses[] = [
            'column' => $column,
            'value' => $value
        ];

        $previousEnd = $fullOffset + strlen($fullMatch);
    }

    $tail = trim(substr($search, $previousEnd));

    if ($tail !== '') {
        if (!preg_match('/^(AND|OR)\s*@?[a-zA-Z_0-9]*:?.*$/i', $tail)) {
            $validStructuredSearch = false;
        }
    }

    if ($validStructuredSearch && count($clauses) > 1) {
        return [
            'mode' => 'compound',
            'column' => null,
            'value' => '',
            'clauses' => $clauses,
            'operators' => $operators
        ];
    }

    if ($validStructuredSearch && count($clauses) === 1 && $operators === []) {
        return [
            'mode' => 'column',
            'column' => $clauses[0]['column'],
            'value' => $clauses[0]['value'],
            'clauses' => $clauses,
            'operators' => []
        ];
    }

    return [
        'mode' => 'global',
        'column' => null,
        'value' => $search,
        'clauses' => [],
        'operators' => []
    ];
}

function getDashboardMapDensity(mysqli $conn, ?string $search = null): array
{
    $search = (string) ($search ?? '');
    $parsed = parseDashboardSearch($search);
    $data = [];

    $sql = "
        SELECT
            l.location_name AS area_name,
            COUNT(s.row_num) AS tick_count
        FROM sighting s
        INNER JOIN location l ON s.location_id = l.location_id
        INNER JOIN species sp ON s.species_id = sp.species_id
    ";

    $params = [];
    $types = '';

    if (($parsed['mode'] === 'column' || $parsed['mode'] === 'compound') && !empty($parsed['clauses'])) {
        $columns = getDashboardSearchColumns();
        $conditions = [];

        foreach ($parsed['clauses'] as $index => $clause) {
            $selectedColumn = $columns[$clause['column']];
            $conditions[] = ($index === 0 ? '' : ' ' . $parsed['operators'][$index - 1] . ' ') . "{$selectedColumn['table']}.{$selectedColumn['column']} LIKE ?";
            $types .= 's';
            $params[] = '%' . $clause['value'] . '%';
        }

        if ($conditions !== []) {
            $sql .= ' WHERE ' . implode('', $conditions) . ' ';
        }
    } elseif ($parsed['mode'] === 'global' && $parsed['value'] !== '') {
        $sql .= "
            WHERE l.location_name LIKE ?
               OR sp.species_name LIKE ?
               OR sp.species_latin_name LIKE ?
        ";
        $searchValue = '%' . $parsed['value'] . '%';
        $types .= 'sss';
        $params[] = $searchValue;
        $params[] = $searchValue;
        $params[] = $searchValue;
    }

    $sql .= "
        GROUP BY l.location_name
        ORDER BY l.location_name
    ";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        return $data;
    }

    if ($params !== []) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data[$row['area_name']] = (int) $row['tick_count'];
        }
    }

    $stmt->close();

    return $data;
}

function getDashboardValueSuggestions(mysqli $conn, string $search, int $limit = 8): array
{
    if (!preg_match('/@([a-zA-Z_][a-zA-Z0-9_]*)\s*:\s*([^@]*)$/i', trim($search), $matches)) {
        return [];
    }

    $columns = getDashboardSearchColumns();
    $column = trim($matches[1]);
    $value = trim($matches[2]);
    $selectedColumn = $columns[$column] ?? null;

    if ($selectedColumn === null) {
        return [];
    }

    $limit = max(1, min($limit, 20));
    $qualifiedColumn = "{$selectedColumn['table']}.{$selectedColumn['column']}";

    $sql = "
        SELECT DISTINCT {$qualifiedColumn} AS suggestion
        FROM sighting s
        INNER JOIN location l ON s.location_id = l.location_id
        INNER JOIN species sp ON s.species_id = sp.species_id
    ";

    $params = [];
    $types = '';

    if ($value !== '') {
        $sql .= " WHERE {$qualifiedColumn} LIKE ? ";
        $types .= 's';
        $params[] = $value . '%';
    }

    $sql .= "
        ORDER BY {$qualifiedColumn}
        LIMIT {$limit}
    ";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        return [];
    }

    if ($params !== []) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $suggestions = [];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $suggestion = trim((string) ($row['suggestion'] ?? ''));

            if ($suggestion !== '') {
                $suggestions[] = $suggestion;
            }
        }
    }

    $stmt->close();

    return $suggestions;
}

if (isset($_GET['ajax']) && $_GET['ajax'] === 'dashboard-search-values') {
    header('Content-Type: application/json');
    echo json_encode([
        'suggestions' => getDashboardValueSuggestions($conn, (string) ($_GET['term'] ?? ''))
    ]);
    exit;
}
