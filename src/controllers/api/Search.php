<?php

require_once ('../src/modules/ControllerBase.php');

require_once '../src/modules/Database/Connector.php';
require_once '../src/modules/Database/Models/Offer.php';

class Controller extends ControllerBase
{
    public function __construct($router)
    {
        parent::__construct($router);
    }

    public function run()
    {
        $dbh = new Database(
            $this->config->get('DB_HOST'),
            $this->config->get('DB_NAME'),
            $this->config->get('DB_USER'),
            $this->config->get('DB_PASS')
        );

        $offset =
            array_key_exists('offset', $_GET)
            ? filter_var($_GET['offset'], FILTER_VALIDATE_INT)
            : 0;
        $limit =
            array_key_exists('limit', $_GET)
            ? filter_var($_GET['limit'], FILTER_VALIDATE_INT)
            : 10;
        $query =
            array_key_exists('query', $_GET)
            ? filter_var($_GET['query'])
            : null;
        $location =
            array_key_exists('location', $_GET)
            ? filter_var($_GET['location'])
            : null;
        $type =
            array_key_exists('type', $_GET)
            ? filter_var($_GET['type'])
            : null;

        if ($offset === false || $offset < 0) {
            $offset = 0;
        }

        if ($limit === false || $limit < 1) {
            $limit = 10;
        }

        if ($type === false || $type === '' || $type === null) {
            $type = 'offer';
        }

        switch ($type) {
            case 'offer':
                $data = $dbh->fetchAll(
                    "SELECT
                        ID_Offer AS ID,
                        Title,
                        Description,
                        Creation_Date,
                        'offer' AS Type
                    FROM Offers
                    WHERE INSTR(Title, :query)
                       OR INSTR(Description, :query)
                    LIMIT $limit
                    OFFSET $offset",
                    array(
                        ':query' => $query
                    )
                );
                $count = $dbh->fetchColumn('SELECT COUNT(*) FROM Offers');
                $matchesCount = $dbh->fetchColumn(
                    "SELECT COUNT(*)
                    FROM Offers
                    WHERE INSTR(Title, :query)
                       OR INSTR(Description, :query)",
                    array(
                        ':query' => $query
                    )
                );
                break;
            case 'company':
                $data = $dbh->fetchAll(
                    "SELECT
                        ID_Company AS ID,
                        Name AS Title,
                        Description,
                        Creation_Date,
                        'company' AS Type
                    FROM Companies
                    WHERE INSTR(Name, :query)
                       OR INSTR(Description, :query)
                    LIMIT $limit
                    OFFSET $offset",
                    array(
                        ':query' => $query
                    )
                );
                $count = $dbh->fetchColumn('SELECT COUNT(*) FROM Companies');
                $matchesCount = $dbh->fetchColumn(
                    "SELECT COUNT(*)
                    FROM Companies
                    WHERE INSTR(Name, :query)
                       OR INSTR(Description, :query)",
                    array(
                        ':query' => $query
                    )
                );
                break;
            case 'user':
                $data = $dbh->fetchAll(
                    "SELECT
                        ID_User AS ID,
                        CONCAT(FirstName, ' ', LastName) AS Title,
                        Description,
                        Creation_Date,
                        'user' AS Type
                    FROM Users
                    WHERE INSTR(FirstName, :query)
                       OR INSTR(LastName, :query)
                       OR INSTR(Description, :query)
                    LIMIT $limit
                    OFFSET $offset",
                    array(
                        ':query' => $query
                    )
                );
                $count = $dbh->fetchColumn('SELECT COUNT(*) FROM Users');
                $matchesCount = $dbh->fetchColumn(
                    "SELECT COUNT(*)
                    FROM Users
                    WHERE INSTR(FirstName, :query)
                       OR INSTR(LastName, :query)
                       OR INSTR(Description, :query)",
                    array(
                        ':query' => $query
                    )
                );
                break;
            default:
                return $this->reply([
                    'error' => 'Invalid type'
                ], 400);
        }

        return $this->reply([
            'total_count' => $count,
            'match_count' => $matchesCount,
            'count' => count($data),
            'data' => $data,
        ]);
    }

    // public function run() {
    //     $regex = [
    //         'search' => '/(?:(?<=^)|(?<![^\s]))(?:(?<type>(?:!?)@[a-z0-9]+)|(?<prop>(?:!?)[a-z0-9]+:(?:(?<!\\\\)"(?:[^"]|\\\\")+(?<!\\\\)"|(?:\\\\\s|[^\s])+))|(?<keywords>(?:!?)(?<!\\\\)"(?:[^"]|\\\\")+(?<!\\\\)")|(?<keyword>(?:!?)(?:\\\\\s|[^\s])+))(?=\s+|$)/mi',
    //         'clean_param' => '/^\$/mi',
    //         'param' => '/^(?<name>[a-z0-9_]+):(?<value>(?:(?<!\\\\)"(?:[^"]|\\\\"|[\t\v\f ])+(?<!\\\\)"|(?:\\\\[\t\v\f ]|[^\s])+))$/mi',
    //         'clean_keyword' => '/^(?<!\\\\)"\s*|\s*(?<!\\\\)"$|\\\\(?=")/mi'
    //     ];

    //     // $data = file_get_contents('../src/controllers/api/data.json');
    //     // $data = json_decode($data, true);

    //     new Database(
    //         $this->config->get('DB_HOST'),
    //         $this->config->get('DB_NAME'),
    //         $this->config->get('DB_USER'),
    //         $this->config->get('DB_PASS')
    //     );

    //     $data = [];

    //     $offers = Offer::getAll(1000);

    //     foreach ($offers as $offer) {
    //         $data[] = [
    //             'ID' => $offer->get('ID'),
    //             'title' => $offer->get('Title'),
    //             'description' => $offer->get('Description'),
    //             'studyLevels' => $offer->getStudyLevels(),
    //             'skills' => $offer->getSkills(),
    //             'type' => 'offer',
    //             'creation_date' => $offer->get('Creation_Date')->format('Y-m-d H:i:s'),
    //         ];
    //     }

    //     // Validate offset and limit
    //     $offset =
    //         array_key_exists('offset', $_GET)
    //         ?  filter_var($_GET['offset'], FILTER_VALIDATE_INT)
    //         : 0;
    //     $limit =
    //         array_key_exists('limit', $_GET)
    //         ? filter_var($_GET['limit'], FILTER_VALIDATE_INT)
    //         : 10;
    //     $query =
    //         array_key_exists('query', $_GET)
    //         ? filter_var($_GET['query'])
    //         : null;
    //     $location =
    //         array_key_exists('location', $_GET)
    //         ? filter_var($_GET['location'])
    //         : null;

    //     // Check if offset is valid
    //     if ($offset === false || $offset < 0 || $offset >= count($data)) {
    //         $offset = 0; // Set default offset
    //     }

    //     // Check if limit is valid
    //     if ($limit === false || $limit < 1 || $limit > count($data)) {
    //         $limit = 10; // Set default limit
    //     }

    //     $filteredData = $data;

    //     if ($query && $query !== '' && $limit) {
    //         // On utilise une regex pour parser la recherche
    //         preg_match_all($regex['search'], $query, $rawMatches, PREG_SET_ORDER, 0);

    //         // On initialise les variables de stockage des résultats
    //         $matches = [
    //             'params' => [],
    //             'keywords' => [],
    //             'types' => []
    //         ];

    //         foreach ($rawMatches as $rawMatch) {
    //             // Si le premier caractère est un "!" alors : 
    //             //     - on le retire
    //             //     - on définit la recherche comme une exclusion
    //             $negative = false;
    //             if ($rawMatch[0][0] === '!') {
    //                 $rawMatch[0] = substr($rawMatch[0], 1);
    //                 $rawMatch['type'] && $rawMatch['type'] = substr($rawMatch['type'], 1);
    //                 $rawMatch['prop'] && $rawMatch['prop'] = substr($rawMatch['prop'], 1);
    //                 $rawMatch['keywords'] && $rawMatch['keywords'] = substr($rawMatch['keywords'], 1);
    //                 $rawMatch['keyword'] && $rawMatch['keyword'] = substr($rawMatch['keyword'], 1);
    //                 $negative = true;
    //             }

    //             if ($rawMatch['type']) { // Type
    //                 // On retire le caractère "@" et on met en minuscule
    //                 $type = strtolower(substr($rawMatch['type'], 1));

    //                 // Si le type est valide, on l'ajoute à la liste des types
    //                 if (in_array($type, ['student', 'offer', 'company'])) $matches['types'][] = [
    //                     'value' => $type,
    //                     'negative' => $negative,
    //                 ];
    //             } else if ($rawMatch['prop']) { // Propriété

    //                 // On retire le caractère "$" et on met en minuscule
    //                 $prop = preg_replace($regex['clean_param'], '', strtolower($rawMatch['prop']));

    //                 // On récupère le nom de la propriété et sa valeur
    //                 preg_match($regex['param'], $prop, $paramMatch);
    //                 $paramMatch['value'] = preg_replace($regex['clean_keyword'], '', $paramMatch['value']);

    //                 $newMatch = [
    //                     'value' => strtolower($paramMatch['value']),
    //                     'negative' => $negative
    //                 ];

    //                 // Si la propriété existe déjà, on ajoute la nouvelle valeur
    //                 if (array_key_exists($paramMatch['name'], $matches['params'])) {
    //                     $matches['params'][$paramMatch['name']] = array_merge(
    //                         $matches['params'][$paramMatch['name']],
    //                         [$newMatch]
    //                     );
    //                 } else {
    //                     // Sinon, on crée la propriété
    //                     $matches['params'][$paramMatch['name']] = [$newMatch];
    //                 }
    //             } else if ($rawMatch['keywords']) { // Mots-clés (multiple mots)
    //                 // On retire les guillemets
    //                 $rawMatch['keywords'] = preg_replace($regex['clean_keyword'], '', $rawMatch['keywords']);

    //                 // On ajoute les mots-clés à la liste des mots-clés
    //                 $matches['keywords'][] = [
    //                     'value' => strtolower($rawMatch['keywords']),
    //                     'negative' => $negative
    //                 ];
    //             } else if ($rawMatch['keyword']) { // Mot-clé (un seul mot)
    //                 $matches['keywords'][] = [
    //                     'value' => strtolower($rawMatch['keyword']),
    //                     'negative' => $negative
    //                 ];
    //             }
    //         }

    //         foreach (array_keys($filteredData) as $itemKey) {
    //             $item = $data[$itemKey];
    //             $score = 0;

    //             foreach (array_keys($item) as $key) { // Chaque propriété de l'entrée dans la BDD
    //                 if (count($matches['keywords'])) {
    //                     // Pour chaque mot-clé, on vérifie si la propriété contient le mot-clé
    //                     // Si c'est le cas, on ajoute 1 au score
    //                     foreach ($matches['keywords'] as $keyword) {
    //                         if (strpos(strtolower($item[$key]), $keyword['value']) !== false) {
    //                             $score += ($keyword['negative'] ? -1 : 1) * 1;
    //                         }
    //                     }
    //                 }
    //                 if (count($matches['params'])) {
    //                     // Pour chaque paramètre, on vérifie si la propriété contient le paramètre
    //                     // Si c'est le cas, on ajoute 10 au score
    //                     foreach (array_keys($matches['params']) as $paramKey) {
    //                         if ($key === $paramKey) {
    //                             foreach ($matches['params'][$paramKey] as $paramMatch) {
    //                                 if (strpos(strtolower($item[$key]), $paramMatch['value']) !== false) {
    //                                     $score += ($paramMatch['negative'] ? -1 : 1) * 10;
    //                                 }
    //                             }
    //                         }
    //                     }
    //                 }
    //                 if (count($matches['types'])) {
    //                     // Pour chaque type, on vérifie si la propriété est du type
    //                     // Si c'est le cas, on ajoute 100 au score
    //                     foreach ($matches['types'] as $type) {
    //                         if (strpos(strtolower($item[$key]), $type['value']) !== false) {
    //                             $score += ($type['negative'] ? -1 : 1) * 100;
    //                         }
    //                     }
    //                 }
    //             }
    //             // Si le score est inférieur à 1, on le met à 0
    //             // afin de ne pas afficher les résultats non pertinents
    //             if ($score < 1) {
    //                 $data[$itemKey]['__score'] = 0;
    //                 continue;
    //             }

    //             // On ajoute le score à l'entrée
    //             $data[$itemKey]['__score'] = $score;
    //         }

    //         // On filtre les résultats pour ne garder que ceux avec un score supérieur à 0
    //         $filteredData = array_values(
    //             array_filter(
    //                 $data,
    //                 function ($item) {
    //                     return $item['__score'] > 0;
    //                 }
    //             )
    //         );

    //         // On trie les résultats par score
    //         usort($filteredData, function ($a, $b) {
    //             return ($b['__score'] - $a['__score']);
    //         });
    //     }

    //     $matchedCount = count($filteredData);
    //     $filteredData = array_slice($filteredData, $offset, $limit);

    //     return $this->reply([
    //         'total_count' => count($data),
    //         'match_count' => $matchedCount,
    //         'count' => count($filteredData),
    //         'matches' => isset($matches) ? $matches : null,
    //         'data' => $filteredData,
    //     ]);
    // }
}
