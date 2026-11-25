<?php
// includes/db.php - Database connection & initialization

function getDB() {
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $dbType = getenv('DB_TYPE') ?: 'sqlite';
    $rootDir = dirname(__DIR__);

    if ($dbType === 'mysql') {
        $host = getenv('DB_HOST') ?: 'localhost';
        $dbname = getenv('DB_NAME') ?: 'utrechtsarchief';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASS') ?: '';
        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    } else {
        $dbFile = $rootDir . '/data/hua_oudegracht.sqlite';
        $isNew = !file_exists($dbFile);
        $pdo = new PDO("sqlite:" . $dbFile, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        if ($isNew || filesize($dbFile) === 0) {
            initDatabase($pdo);
        }
    }

    return $pdo;
}

function initDatabase(PDO $pdo) {
    // 1. Create tables
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            password_hash TEXT NOT NULL,
            role TEXT DEFAULT 'admin',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS route_points (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            code TEXT NOT NULL,
            sort_order INTEGER NOT NULL DEFAULT 0,
            video_start_sec INTEGER NOT NULL,
            video_end_sec INTEGER NOT NULL,
            latitude REAL NOT NULL,
            longitude REAL NOT NULL,
            is_bridge INTEGER DEFAULT 0,
            is_active INTEGER DEFAULT 1
        );

        CREATE TABLE IF NOT EXISTS route_translations (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            route_point_id INTEGER NOT NULL,
            lang_code TEXT NOT NULL,
            title TEXT NOT NULL,
            description TEXT NOT NULL,
            FOREIGN KEY (route_point_id) REFERENCES route_points(id) ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS media_items (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            route_point_id INTEGER NOT NULL,
            image_path TEXT NOT NULL,
            archive_url TEXT DEFAULT '',
            caption_nl TEXT DEFAULT '',
            caption_en TEXT DEFAULT '',
            alt_text_nl TEXT DEFAULT '',
            alt_text_en TEXT DEFAULT '',
            sort_order INTEGER DEFAULT 0,
            FOREIGN KEY (route_point_id) REFERENCES route_points(id) ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS settings (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            setting_key TEXT UNIQUE NOT NULL,
            value_nl TEXT NOT NULL,
            value_en TEXT NOT NULL,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
    ");

    // 2. Insert default admin (admin / admin123)
    $stmt = $pdo->prepare("INSERT OR IGNORE INTO users (id, username, password_hash, role) VALUES (1, 'admin', :hash, 'admin')");
    $stmt->execute(['hash' => password_hash('admin123', PASSWORD_DEFAULT)]);

    // 3. Insert default settings (Colofon & Intro)
    $colofonNL = "Deze interactieve webapplicatie en het beheersysteem zijn ontwikkeld voor Het Utrechts Archief ter gelegenheid van het 900-jarig bestaan van de Oudegracht in Utrecht (1127–2027) en de tijdelijke tentoonstelling 'Ontmoet de Oudegracht'.\n\n"
        . "Opdrachtgever: Het Utrechts Archief (HUA)\n"
        . "Contactpersoon & Begeleider: Ellen Blom (Educator)\n"
        . "Bezoekadres: Hamburgerstraat 28, 3512 NS Utrecht\n"
        . "Website: www.hetutrechtsarchief.nl\n\n"
        . "Ontwerp & Realisatie:\n"
        . "Mylène Zijlstra & Iris Govaard (Grafisch Lyceum Utrecht)\n\n"
        . "Bronmateriaal & Rechten: Het Utrechts Archief. De historische filmopname toont de Utrechtse Oudegracht in 1929 van de Gaardbrug tot de Stadhuisbrug.";

    $colofonEN = "This interactive web application and CMS were developed for Het Utrechts Archief on the occasion of the 900th anniversary of the Oudegracht canal in Utrecht (1127–2027) and the temporary exhibition 'Meet the Oudegracht'.\n\n"
        . "Client: Het Utrechts Archief (HUA)\n"
        . "Contact & Educator: Ellen Blom\n"
        . "Address: Hamburgerstraat 28, 3512 NS Utrecht\n"
        . "Website: www.hetutrechtsarchief.nl\n\n"
        . "Design & Development:\n"
        . "Mylène Zijlstra & Iris Govaard (Grafisch Lyceum Utrecht)\n\n"
        . "Archival Sources & Rights: Het Utrechts Archief. The historical film footage documents the Oudegracht canal in 1929 from Gaardbrug to Stadhuisbrug.";

    $introNL = "Welkom bij 'Ontmoet de Oudegracht' (1929)! Stap virtueel aan boord van de historische rondvaartboot en ontdek de Utrechtse Oudegracht aan de hand van unieke filmbeelden uit 1929 en origineel archiefmateriaal. Klik op de kaart of sleep over de tijdlijn om naar een locatie te varen.";
    $introEN = "Welcome to 'Meet the Oudegracht' (1929)! Step aboard the historic boat tour and explore Utrecht's famous Oudegracht through unique 1929 film footage and authentic archival treasures. Click on the map or drag the timeline to sail directly to a landmark.";

    $stmt = $pdo->prepare("INSERT OR IGNORE INTO settings (setting_key, value_nl, value_en) VALUES (?, ?, ?)");
    $stmt->execute(['colofon', $colofonNL, $colofonEN]);
    $stmt->execute(['intro', $introNL, $introEN]);

    // 4. Seed Route Points from Bronmateriaal Oudegracht
    $points = [
        [
            'code' => '1',
            'sort_order' => 1,
            'start' => 0,
            'end' => 14,
            'lat' => 52.08985,
            'lng' => 5.12153,
            'is_bridge' => 1,
            'title_nl' => '1. De Gaardbrug',
            'desc_nl' => "De Gaardbrug is een van de bruggen over de Utrechtse Oudegracht en ontleent haar naam aan de vroegere bisschoppelijke tuin, die ook wel de ‘gaard’ werd genoemd. Vanaf de brug is er zicht op de Oudegracht en de Domtoren, dit maakt het een populaire plek om foto’s te maken.",
            'title_en' => '1. The Gaardbrug',
            'desc_en' => "The Gaardbrug is one of the historic bridges across Utrecht's Oudegracht. Its name derives from the bishop's orchard ('gaard'). Looking from the bridge offers an iconic view of the Oudegracht and the Dom Tower, making it a beloved photo spot.",
            'images' => [
                ['file' => 'X186989 - B 812-K.jpg', 'url' => 'https://hetutrechtsarchief.nl/beeld/E154E77CBAE1BBADE0538F04000A554F', 'caption_nl' => 'Gezicht op de Gaardbrug en Oudegracht met Domtoren', 'caption_en' => 'View of Gaardbrug and canal with the Dom Tower', 'alt_nl' => 'Historische foto van de Gaardbrug over de Oudegracht met de Domtoren op de achtergrond', 'alt_en' => 'Historic photo of Gaardbrug across Oudegracht with the Dom Tower'],
                ['file' => 'X103875 - 1587.jpg', 'url' => 'https://hetutrechtsarchief.nl/beeld/0B35692A2EFE5C49924AB82E3B6FEA43', 'caption_nl' => 'Zicht op de Gaardbrug vanaf het water', 'caption_en' => 'View of Gaardbrug from the canal water', 'alt_nl' => 'Gezicht op de Gaardbrug vanaf een boot', 'alt_en' => 'View of Gaardbrug from a canal boat']
            ]
        ],
        [
            'code' => '1a',
            'sort_order' => 2,
            'start' => 14,
            'end' => 26,
            'lat' => 52.09025,
            'lng' => 5.12110,
            'is_bridge' => 0,
            'title_nl' => '1a. De middeleeuwse werven en werfkelders',
            'desc_nl' => "Langs de gracht zie je de bijzondere combinatie van een hoger gelegen straat en een lager gelegen werf aan het water. Handelaren gebruikten de werfkelders om goederen op te slaan en rechtstreeks vanaf schepen aan te voeren. Zo groeide de Oudegracht uit tot een belangrijke handelsroute door de stad.",
            'title_en' => '1a. The Medieval Wharves & Wharf Cellars',
            'desc_en' => "Along the canal you see the unique two-tiered system: a street level above and a wharf level right at the water. Merchants stored goods directly in the wharf cellars supplied straight from trading vessels.",
            'images' => [
                ['file' => 'X174241 - 406922.jpg', 'url' => 'https://hetutrechtsarchief.nl/beeld/17304FEB14DD50B580B52031DBDAE08C', 'caption_nl' => 'De karakteristieke werven en werfkelders van Utrecht', 'caption_en' => 'The characteristic wharves and cellars of Utrecht', 'alt_nl' => 'Historische opname van middeleeuwse werven en werfkelders langs het water', 'alt_en' => 'Historical photo of medieval wharves and wharf cellars along the water']
            ]
        ],
        [
            'code' => '1b',
            'sort_order' => 3,
            'start' => 26,
            'end' => 38,
            'lat' => 52.09050,
            'lng' => 5.12075,
            'is_bridge' => 0,
            'title_nl' => '1b. Café De Postillon / Gaardbrug OG 148',
            'desc_nl' => "Café De Postillon aan de Oudegracht 148 nabij de Gaardbrug. Dit historische pand met werfkelder getuigt van de levendige horeca en handel die al eeuwenlang rond de grachten plaatsvindt.",
            'title_en' => '1b. Café De Postillon / Gaardbrug OG 148',
            'desc_en' => "Café De Postillon at Oudegracht 148 near the Gaardbrug. This historic building with its wharf cellar is testament to centuries of bustling hospitality and trade along the canals.",
            'images' => [
                ['file' => 'X114188 - M 1642.jpg', 'url' => 'https://hetutrechtsarchief.nl/beeld/FF48325FF02695B5E0538F04000A4889', 'caption_nl' => 'Café De Postillon aan de Oudegracht 148', 'caption_en' => 'Café De Postillon at Oudegracht 148', 'alt_nl' => 'Historische foto van Café De Postillon aan de Oudegracht', 'alt_en' => 'Historic photo of Café De Postillon at Oudegracht']
            ]
        ],
        [
            'code' => '1c',
            'sort_order' => 4,
            'start' => 38,
            'end' => 52,
            'lat' => 52.09085,
            'lng' => 5.12030,
            'is_bridge' => 0,
            'title_nl' => '1c. Lijnmarkt / Lichte Gaard',
            'desc_nl' => "De Lijnmarkt en de Lichte Gaard vormen het hart van het historische winkel- en ambachtsgebied langs de Oudegracht. Waar vroeger touwslagers en kooplieden hun waren verkochten, wandel je vandaag langs sfeervolle boetieks en terrassen.",
            'title_en' => '1c. Lijnmarkt / Lichte Gaard',
            'desc_en' => "Lijnmarkt and Lichte Gaard form the heart of the historic shopping district along the Oudegracht, originally home to ropemakers and merchants.",
            'images' => [
                ['file' => 'X176657 - R 605.jpg', 'url' => 'https://hetutrechtsarchief.nl/beeld/B92A187339A5B6C3E0534701000ABA07', 'caption_nl' => 'Zicht op de Lijnmarkt en Lichte Gaard', 'caption_en' => 'View of Lijnmarkt and Lichte Gaard', 'alt_nl' => 'Gezicht op de winkelstraat Lijnmarkt en Lichte Gaard', 'alt_en' => 'Historic street scene of Lijnmarkt and Lichte Gaard'],
                ['file' => 'X176658 - R 606.jpg', 'url' => 'https://hetutrechtsarchief.nl/beeld/74208D558E535B50BA7C6690258D82BD', 'caption_nl' => 'Winkelpanden en levendigheid aan de Lichte Gaard', 'caption_en' => 'Historic shops and street life at Lichte Gaard', 'alt_nl' => 'Historische gevels aan de Lichte Gaard', 'alt_en' => 'Historic facades along Lichte Gaard'],
                ['file' => 'X176668 - R 617.jpg', 'url' => 'https://hetutrechtsarchief.nl/beeld/FCC5D27D5122834BE0538F04000A363D', 'caption_nl' => 'De boogbruggen en doorkijk naar de Lijnmarkt', 'caption_en' => 'Arch bridges and vista to Lijnmarkt', 'alt_nl' => 'Doorkijk over de Oudegracht richting Lijnmarkt', 'alt_en' => 'Canal vista toward Lijnmarkt']
            ]
        ],
        [
            'code' => '2',
            'sort_order' => 5,
            'start' => 52,
            'end' => 68,
            'lat' => 52.09115,
            'lng' => 5.11995,
            'is_bridge' => 1,
            'title_nl' => '2. Maartensbrug',
            'desc_nl' => "De Maartensbrug is de oudste brug over de Utrechtse Oudegracht. De brug werd oorspronkelijk de Borchbrug genoemd, omdat hij toegang gaf tot de bisschoppelijke burcht op de plek van het huidige Domplein. De eerste stenen brug werd in 1404 gebouwd en in 1906 verbreed om ruimte te maken voor het toenemende verkeer en de paardentram. De huidige naam verwijst naar Sint-Maarten, de beschermheilige van Utrecht.",
            'title_en' => '2. Maartensbrug',
            'desc_en' => "The Maartensbrug is the oldest bridge over the Oudegracht, originally called the Borchbrug because it connected to the episcopal castle at Domplein. Built in stone in 1404 and widened in 1906 for the horse tram. Named after Saint Martin, Utrecht's patron saint.",
            'images' => [
                ['file' => 'X116883 - 603347.jpg', 'url' => 'https://hetutrechtsarchief.nl/beeld/C744769E072555D49E2E03E92DBCB604', 'caption_nl' => 'De historische Maartensbrug met doorkijk naar de Dom', 'caption_en' => 'The historic Maartensbrug with view to the Dom', 'alt_nl' => 'Zicht op de Maartensbrug over de Oudegracht met Domtoren', 'alt_en' => 'View of Maartensbrug bridge over the canal with Dom Tower'],
                ['file' => 'X12483 - M4338.jpg', 'url' => 'https://hetutrechtsarchief.nl/beeld/62665B8EA2525EC3A01EDA19075CF809', 'caption_nl' => 'Paardentram en verkeer over de Maartensbrug', 'caption_en' => 'Horse tram and traffic crossing the Maartensbrug', 'alt_nl' => 'Historische foto van verkeer op de Maartensbrug', 'alt_en' => 'Historic photo of early traffic on Maartensbrug'],
                ['file' => 'X157246 - L 8587.jpg', 'url' => 'https://hetutrechtsarchief.nl/beeld/B2BD88E3DE5B2173E0534701000A486F', 'caption_nl' => 'De Maartensbrug vanaf het water gezien', 'caption_en' => 'The Maartensbrug seen from the water level', 'alt_nl' => 'Gezicht vanaf een boot op de Maartensbrug', 'alt_en' => 'View of Maartensbrug bridge from a boat']
            ]
        ],
        [
            'code' => '2a',
            'sort_order' => 6,
            'start' => 68,
            'end' => 80,
            'lat' => 52.09160,
            'lng' => 5.11945,
            'is_bridge' => 0,
            'title_nl' => '2a. Vismarkt',
            'desc_nl' => "De Vismarkt is de oudste markt van Utrecht. Al eeuwenlang wordt op het gedeelte van de Oudegracht tussen de Stadhuisbrug en de Maartensbrug vis verhandeld. Dit deel van de gracht ontleent zijn naam dan ook aan de markt.",
            'title_en' => '2a. Fish Market (Vismarkt)',
            'desc_en' => "The Vismarkt is Utrecht's oldest market. For centuries, fish was sold on the stretch of the Oudegracht between Stadhuisbrug and Maartensbrug, giving this section its name.",
            'images' => [
                ['file' => 'X158973 - R 0020.jpg', 'url' => 'https://hetutrechtsarchief.nl/beeld/72DD2DCB9BAC546FBBBCF5C1111CC735', 'caption_nl' => 'Marktkramen en visverkoop aan de Vismarkt', 'caption_en' => 'Market stalls and fishmongers at Vismarkt', 'alt_nl' => 'Historische viskramen langs de werf aan de Vismarkt', 'alt_en' => 'Historic fish market stalls along the wharf at Vismarkt']
            ]
        ],
        [
            'code' => '3',
            'sort_order' => 7,
            'start' => 80,
            'end' => 92,
            'lat' => 52.09195,
            'lng' => 5.11910,
            'is_bridge' => 1,
            'title_nl' => '3. Kalisbrug',
            'desc_nl' => "De Kalisbrug, ook wel Visbrug genoemd, vormde een belangrijk onderdeel van de Vismarkt en diende als visafslag. Wanneer er voldoende vis was aangevoerd, kondigde de stadsomroeper de verkoop aan. De vis werd vers gehouden in viskaren die in de gracht hingen en verkocht vanaf visbanken op de brug. De traditionele vismarkt verdween aan het begin van de 20e eeuw, maar in de winkels aan de Vismarkt bleven nog lange tijd visverkopers actief. De laatste traditionele viswinkel sloot in 1970.",
            'title_en' => '3. Kalisbrug',
            'desc_en' => "The Kalisbrug, also known as Visbrug, served as the fish auction. The town crier announced fresh catches kept alive in submerged corfs. Fish stalls operated on the bridge until the last traditional fish shop closed in 1970.",
            'images' => [
                ['file' => 'X115043 - 22275.jpg', 'url' => 'https://hetutrechtsarchief.nl/beeld/452C4EF2E0F15EDA9ACADF86F8E57F51', 'caption_nl' => 'De Kalisbrug / Visbrug over de Oudegracht', 'caption_en' => 'Kalisbrug / Visbrug across the Oudegracht', 'alt_nl' => 'Historische foto van de Kalisbrug met de visbanken', 'alt_en' => 'Historic photo of Kalisbrug bridge with fish stalls'],
                ['file' => 'X176680 - R 630.jpg', 'url' => 'https://hetutrechtsarchief.nl/beeld/B92F162A1FAB12C0E0534701000A2676', 'caption_nl' => 'Visverkopers en omstanders op de Kalisbrug', 'caption_en' => 'Fish vendors and citizens on Kalisbrug', 'alt_nl' => 'Levendigheid op de Kalisbrug met visverkopers', 'alt_en' => 'Lively scene on Kalisbrug with fish vendors'],
                ['file' => 'X191975 - M 8493.jpg', 'url' => 'https://hetutrechtsarchief.nl/beeld/B92C390A7BF7CB12E0534701000AB712', 'caption_nl' => 'Gezicht op de Kalisbrug vanaf de waterlijn', 'caption_en' => 'View of Kalisbrug from the water line', 'alt_nl' => 'Kalisbrug gezien vanaf het wateroppervlak', 'alt_en' => 'Kalisbrug seen from water level']
            ]
        ],
        [
            'code' => '5',
            'sort_order' => 8,
            'start' => 92,
            'end' => 105,
            'lat' => 52.09240,
            'lng' => 5.11865,
            'is_bridge' => 1,
            'title_nl' => '5. De Stadhuisbrug en het stadhuis',
            'desc_nl' => "De Stadhuisbrug dankt haar naam aan het Utrechtse stadhuis dat direct aan de brug ligt. Op deze plek zetelde het stadsbestuur al vanaf de 14e eeuw. De brug ontstond in de 16e eeuw, toen twee bestaande bruggen, de Broodbrug en de Huidenbrug, met elkaar werden verbonden en overkluisd. Het plein dat hierdoor ontstond, werd gebruikt voor openbare afkondigingen van besluiten van het stadsbestuur en kreeg later de naam Stadhuisbrug.",
            'title_en' => '5. Stadhuisbrug and City Hall',
            'desc_en' => "Stadhuisbrug is named after Utrecht's historic City Hall adjacent to the bridge, home to city governance since the 14th century. Formed in the 16th century by vaulting together the Broodbrug and Huidenbrug, creating an open square for official proclamations.",
            'images' => [
                ['file' => 'X192995 - B 2451-G.jpg', 'url' => 'https://hetutrechtsarchief.nl/beeld/AA8056D41AB75229B8D85A70192C714D', 'caption_nl' => 'De overkluisde Stadhuisbrug en het stadhuis', 'caption_en' => 'The vaulted Stadhuisbrug and City Hall', 'alt_nl' => 'Het indrukwekkende stadhuis van Utrecht aan de Stadhuisbrug', 'alt_en' => 'Utrecht City Hall on the Stadhuisbrug'],
                ['file' => 'X193826 - 903109.jpg', 'url' => 'https://hetutrechtsarchief.nl/beeld/E368FFAC3A8A51798AB601A94CEAB7AE', 'caption_nl' => 'Zicht op de Stadhuisbrug vanaf het water', 'caption_en' => 'View of Stadhuisbrug from the canal', 'alt_nl' => 'Historische bootpassage onder de Stadhuisbrug', 'alt_en' => 'Historic boat passage under Stadhuisbrug bridge'],
                ['file' => 'X244210 - 914725.jpg', 'url' => 'https://hetutrechtsarchief.nl/beeld/DB73D181F47BBAE0E0538F04000AC07C', 'caption_nl' => 'Stadhuisbrug met winkelpubliek en plein', 'caption_en' => 'Stadhuisbrug square with shoppers', 'alt_nl' => 'Gezicht over het plein van de Stadhuisbrug', 'alt_en' => 'View across Stadhuisbrug bridge square']
            ]
        ]
    ];

    $stmtPoint = $pdo->prepare("
        INSERT INTO route_points (code, sort_order, video_start_sec, video_end_sec, latitude, longitude, is_bridge, is_active)
        VALUES (:code, :sort_order, :start, :end, :lat, :lng, :is_bridge, 1)
    ");
    $stmtTrans = $pdo->prepare("
        INSERT INTO route_translations (route_point_id, lang_code, title, description)
        VALUES (:pt_id, :lang, :title, :desc)
    ");
    $stmtMedia = $pdo->prepare("
        INSERT INTO media_items (route_point_id, image_path, archive_url, caption_nl, caption_en, alt_text_nl, alt_text_en, sort_order)
        VALUES (:pt_id, :path, :url, :cap_nl, :cap_en, :alt_nl, :alt_en, :sort)
    ");

    foreach ($points as $pt) {
        $stmtPoint->execute([
            'code' => $pt['code'],
            'sort_order' => $pt['sort_order'],
            'start' => $pt['start'],
            'end' => $pt['end'],
            'lat' => $pt['lat'],
            'lng' => $pt['lng'],
            'is_bridge' => $pt['is_bridge']
        ]);
        $pointId = $pdo->lastInsertId();

        $stmtTrans->execute([
            'pt_id' => $pointId,
            'lang' => 'nl',
            'title' => $pt['title_nl'],
            'desc' => $pt['desc_nl']
        ]);
        $stmtTrans->execute([
            'pt_id' => $pointId,
            'lang' => 'en',
            'title' => $pt['title_en'],
            'desc' => $pt['desc_en']
        ]);

        $sort = 1;
        foreach ($pt['images'] as $img) {
            $stmtMedia->execute([
                'pt_id' => $pointId,
                'path' => 'assets/images/' . $img['file'],
                'url' => $img['url'],
                'cap_nl' => $img['caption_nl'],
                'cap_en' => $img['caption_en'],
                'alt_nl' => $img['alt_nl'],
                'alt_en' => $img['alt_en'],
                'sort' => $sort++
            ]);
        }
    }
}
