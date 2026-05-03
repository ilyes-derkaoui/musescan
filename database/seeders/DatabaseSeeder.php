<?php

namespace Database\Seeders;

use App\Models\Artifact;
use App\Models\ArtifactMedia;
use App\Models\Category;
use App\Models\HistoricalFigure;
use App\Models\Translation;
use App\Models\User;
use App\Support\QrCodeGenerator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@museum.local'],
            [
                'name' => 'Administrateur',
                'password' => Hash::make('museum-admin-2026'),
            ]
        );

        $arms = Category::firstOrCreate(['name' => 'Armes historiques']);
        $docs = Category::firstOrCreate(['name' => 'Documents militaires']);
        $figures = Category::firstOrCreate(['name' => 'Figures historiques']);

        $artifactData = [
            [
                'name' => 'Sabre de l Emir Abd el-Kader',
                'floor' => 1,
                'category_id' => $arms->id,
                'qr_code' => 'artifact-001',
                'has_3d_model' => true,
                'model_3d_path' => 'https://modelviewer.dev/shared-assets/models/Astronaut.glb',
                'translations' => [
                    'ar' => ['name' => 'سيف الأمير عبد القادر', 'description' => 'سيف تاريخي من القرن التاسع عشر يرمز إلى مقاومة الاستعمار.'],
                    'fr' => ['name' => 'Sabre de l Emir Abd el-Kader', 'description' => 'Sabre historique du XIXe siecle, symbole de resistance nationale.'],
                    'en' => ['name' => 'Sword of Emir Abd el-Kader', 'description' => 'A 19th-century sword symbolizing national resistance.'],
                    'es' => ['name' => 'Espada del Emir Abd el-Kader', 'description' => 'Espada historica del siglo XIX, simbolo de resistencia nacional.'],
                    'zh' => ['name' => '埃米尔阿卜杜勒卡迪尔之剑', 'description' => '十九世纪历史名剑，象征民族抵抗精神。'],
                    'ru' => ['name' => 'Сабля эмира Абд аль-Кадира', 'description' => 'Историческая сабля XIX века, символ национального сопротивления.'],
                ],
            ],
            [
                'name' => 'Proclamation du 1er Novembre 1954',
                'floor' => 2,
                'category_id' => $docs->id,
                'qr_code' => 'artifact-002',
                'has_3d_model' => false,
                'model_3d_path' => '',
                'translations' => [
                    'ar' => ['name' => 'بيان أول نوفمبر 1954', 'description' => 'وثيقة تاريخية تعلن بداية الثورة الجزائرية.'],
                    'fr' => ['name' => 'Proclamation du 1er Novembre 1954', 'description' => 'Document fondateur annoncant le debut de la revolution algerienne.'],
                    'en' => ['name' => 'Proclamation of November 1, 1954', 'description' => 'Foundational document announcing the Algerian revolution.'],
                    'es' => ['name' => 'Proclamacion del 1 de Noviembre de 1954', 'description' => 'Documento fundacional que anuncia la revolucion argelina.'],
                    'zh' => ['name' => '1954年11月1日宣言', 'description' => '宣布阿尔及利亚革命开始的奠基文件。'],
                    'ru' => ['name' => 'Прокламация 1 ноября 1954 года', 'description' => 'Основополагающий документ, объявивший начало Алжирской революции.'],
                ],
            ],
            [
                'name' => 'Portrait du Colonel Amirouche',
                'floor' => 3,
                'category_id' => $figures->id,
                'qr_code' => 'artifact-003',
                'has_3d_model' => true,
                'model_3d_path' => 'https://modelviewer.dev/shared-assets/models/RobotExpressive.glb',
                'translations' => [
                    'ar' => ['name' => 'صورة العقيد عميروش', 'description' => 'عرض تذكاري لاحد قادة الثورة التحريرية الجزائرية.'],
                    'fr' => ['name' => 'Portrait du Colonel Amirouche', 'description' => 'Presentation commemorative de l un des grands chefs de la revolution.'],
                    'en' => ['name' => 'Portrait of Colonel Amirouche', 'description' => 'Commemorative display of one of the great leaders of the revolution.'],
                    'es' => ['name' => 'Retrato del Coronel Amirouche', 'description' => 'Presentacion conmemorativa de uno de los grandes lideres revolucionarios.'],
                    'zh' => ['name' => '阿米鲁什上校肖像', 'description' => '纪念阿尔及利亚革命重要领导者的展品。'],
                    'ru' => ['name' => 'Портрет полковника Амируша', 'description' => 'Памятная экспозиция, посвященная одному из выдающихся лидеров революции.'],
                ],
            ],
            [
                'name' => 'Portrait officiel de Houari Boumédiène',
                'floor' => 2,
                'category_id' => $figures->id,
                'qr_code' => 'artifact-004',
                'has_3d_model' => true,
                // Photogrammetry-grade 3D is not available yet; use a neutral placeholder model.
                'model_3d_path' => 'https://modelviewer.dev/shared-assets/models/Astronaut.glb',
                'translations' => [
                    'ar' => [
                        'name' => 'الرئيس الراحل هواري بومدين',
                        'description' => 'هواري بومدين، واسمه الحقيقي محمد بوخروبة، يُعد من أبرز الشخصيات في تاريخ الجزائر الحديثة. وُلد في 23 أوت 1932 قرب قالمة خلال فترة الاستعمار الفرنسي. تلقى تعليمه في الجزائر ثم في تونس ومصر، وانخرط مبكراً في العمل الوطني. أثناء الثورة التحريرية برز كقائد عسكري داخل جبهة التحرير الوطني حتى أصبح رئيس الأركان. بعد الاستقلال تولى وزارة الدفاع، ثم قاد في 1965 ما سُمّي بالتصحيح الثوري وأصبح حاكم البلاد. ركّز على بناء دولة قوية ذات سيادة، وعلى التصنيع والإصلاحات الاشتراكية، وكان قرار تأميم المحروقات سنة 1971 من أهم محطاته. كما عزز التعليم والصحة والبنية التحتية، ولعب دوراً دولياً بارزاً في حركة عدم الانحياز. رغم الانتقادات المرتبطة بطابع الحكم المركزي، يبقى أحد أبرز بناة الدولة الجزائرية الحديثة. 19 جوان 1965 – 27 ديسمبر 1978.',
                    ],
                    'fr' => [
                        'name' => 'Le président défunt Houari Boumédiène',
                        'description' => 'Houari Boumédiène, né Mohamed Boukherouba en 1932 près de Guelma, est une figure centrale de l’Algérie indépendante. Formé en Algérie, à Tunis et au Caire, il s’engage tôt dans le mouvement nationaliste. Durant la Guerre d’Algérie, il devient un cadre militaire majeur du FLN et accède au poste de chef d’état-major. Après 1962, il est ministre de la Défense puis prend le pouvoir en 1965. Son projet politique vise un État fort et souverain, avec une industrialisation soutenue et la nationalisation des hydrocarbures en 1971. Il impulse aussi des progrès dans l’éducation, la santé et les infrastructures. Sur le plan international, il s’impose dans le mouvement des non-alignés et porte la voix du tiers-monde. Son régime est toutefois critiqué pour son autoritarisme. Son héritage demeure majeur dans la construction de l’Algérie moderne. 19 juin 1965 – 27 décembre 1978.',
                    ],
                    'en' => [
                        'name' => 'The late President Houari Boumédiène',
                        'description' => 'Houari Boumédiène, born Mohamed Boukherouba in 1932 near Guelma, is one of the most influential leaders in modern Algerian history. Educated in Algeria, Tunisia, and Egypt, he joined anti-colonial activism at an early stage. During the Algerian War of Independence, he became a key FLN military leader and later chief of staff. After independence, he served as Minister of Defense and took power in 1965. He focused on building a strong, sovereign state through socialist reforms and large industrial projects, including the landmark 1971 nationalization of oil and gas resources. He also invested in education, healthcare, and infrastructure, while promoting Algeria’s role in the Non-Aligned Movement and the Global South. His rule was criticized for limited political freedoms, yet his legacy remains foundational for modern Algeria. June 19, 1965 – December 27, 1978.',
                    ],
                    'es' => [
                        'name' => 'El difunto presidente Houari Boumédiène',
                        'description' => 'Houari Boumédiène, cuyo nombre real era Mohamed Boukherouba, nació en 1932 cerca de Guelma. Se formó en Argelia, Túnez y Egipto, donde fortaleció su conciencia política. Durante la guerra de independencia, se convirtió en un dirigente militar clave del FLN y llegó a jefe del estado mayor. Tras la independencia fue ministro de Defensa y tomó el poder en 1965. Impulsó la construcción de un Estado fuerte, aplicó reformas socialistas y promovió una industrialización amplia, destacando la nacionalización de los hidrocarburos en 1971. También apoyó la educación, la salud y la modernización de infraestructuras. En la escena internacional, fue una figura importante del movimiento de países no alineados. Aunque su sistema fue criticado por su centralización política, sigue siendo considerado uno de los principales constructores de la Argelia moderna. 19 de junio de 1965 – 27 de diciembre de 1978.',
                    ],
                    'zh' => [
                        'name' => '已故总统 胡阿里·布迈丁',
                        'description' => '胡阿里·布迈丁（本名穆罕默德·布赫鲁巴）是阿尔及利亚现代史上最重要的领导人之一。1932年出生于盖勒马附近，在阿尔及利亚、突尼斯和埃及接受教育。独立战争期间，他成为民族解放阵线的重要军事领导人，并最终担任总参谋长。独立后，他任国防部长，并于1965年掌握政权。其治国重点是建设主权国家、推进工业化与社会主义改革；1971年实现油气资源国有化，被视为关键历史节点。他还推动教育、医疗和基础设施发展，并在不结盟运动中提升了阿尔及利亚的国际影响力。尽管其统治因政治自由不足而受到批评，但他仍被广泛视为现代阿尔及利亚国家建设的重要奠基者。1965年6月19日 – 1978年12月27日。',
                    ],
                    'ru' => [
                        'name' => 'Покойный президент Хуари Бумедьен',
                        'description' => 'Хуари Бумедьен (настоящее имя Мохамед Бухеруба) — один из ключевых лидеров современного Алжира. Он родился в 1932 году недалеко от Гельмы, получил образование в Алжире, Тунисе и Египте и рано включился в национально-освободительное движение. В годы войны за независимость он стал важным военным руководителем ФНО и поднялся до должности начальника штаба. После независимости занял пост министра обороны, а в 1965 году пришёл к власти. Его курс был направлен на построение сильного суверенного государства: индустриализация, социалистические реформы и национализация нефтегазового сектора в 1971 году. Он также развивал образование, здравоохранение и инфраструктуру, укрепляя международные позиции Алжира в движении неприсоединения. Несмотря на критику за авторитарность, его считают одним из главных архитекторов современного алжирского государства. 19 июня 1965 г. – 27 декабря 1978 г.',
                    ],
                ],
            ],
        ];

        foreach ($artifactData as $item) {
            $artifact = Artifact::updateOrCreate(
                ['qr_code' => $item['qr_code']],
                [
                    'name' => $item['name'],
                    'floor' => $item['floor'],
                    'category_id' => $item['category_id'],
                    'has_3d_model' => $item['has_3d_model'],
                ]
            );

            if ($artifact->qr_image_path && Storage::disk('public')->exists($artifact->qr_image_path)) {
                Storage::disk('public')->delete($artifact->qr_image_path);
            }

            $artifact->update([
                'qr_image_path' => QrCodeGenerator::generateForArtifact($artifact->qr_code),
            ]);

            foreach ($item['translations'] as $locale => $translation) {
                Translation::updateOrCreate(
                    ['artifact_id' => $artifact->id, 'locale' => $locale],
                    ['name' => $translation['name'], 'description' => $translation['description']]
                );
            }

            if ($item['model_3d_path'] !== '') {
                ArtifactMedia::updateOrCreate(
                    ['artifact_id' => $artifact->id, 'type' => 'model_3d', 'is_main' => true],
                    ['file_path' => $item['model_3d_path']]
                );
            }

            if ($item['qr_code'] === 'artifact-004') {
                $imageSet = [
                    ['file_path' => 'images/artifacts/boumediene/houari-portrait.png', 'is_main' => true],
                    ['file_path' => 'images/artifacts/boumediene/houari-vintage.png', 'is_main' => false],
                    ['file_path' => 'images/artifacts/boumediene/view-front.png', 'is_main' => false],
                    ['file_path' => 'images/artifacts/boumediene/view-right.png', 'is_main' => false],
                    ['file_path' => 'images/artifacts/boumediene/view-right-top.png', 'is_main' => false],
                    ['file_path' => 'images/artifacts/boumediene/view-left.png', 'is_main' => false],
                    ['file_path' => 'images/artifacts/boumediene/view-left-bottom.png', 'is_main' => false],
                    ['file_path' => 'images/artifacts/boumediene/view-back.png', 'is_main' => false],
                ];

                foreach ($imageSet as $media) {
                    ArtifactMedia::updateOrCreate(
                        [
                            'artifact_id' => $artifact->id,
                            'type' => 'image',
                            'file_path' => $media['file_path'],
                        ],
                        ['is_main' => $media['is_main']]
                    );
                }
            }
        }

        HistoricalFigure::updateOrCreate(
            ['name' => 'Colonel Amirouche'],
            ['birth_year' => 1926, 'death_year' => 1959, 'artifact_id' => Artifact::where('qr_code', 'artifact-003')->value('id')]
        );

        HistoricalFigure::updateOrCreate(
            ['name' => 'Houari Boumediene'],
            ['birth_year' => 1932, 'death_year' => 1978, 'artifact_id' => Artifact::where('qr_code', 'artifact-004')->value('id')]
        );
    }
}
