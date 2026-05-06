<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Artifact;
use App\Models\Category;
use App\Models\Translation;
use App\Models\ArtifactMedia;

/**
 * ArtifactSeeder
 *
 * Seeds 3 test artifacts so the QR scanner has real data to return.
 *
 * QR codes to print (use any free QR generator, choose "Text" type):
 *   artifact-001  →  Sabre de l'Émir Abd el-Kader
 *   artifact-002  →  Proclamation du 1er Novembre 1954
 *   artifact-003  →  Médaille du Moudjahid
 *
 * 3D models used are free GLBs from the official Khronos glTF sample library
 * and Google's model-viewer demo assets — no login or download needed.
 * Replace with your real .glb files once you have them.
 *
 * Run with: php artisan db:seed --class=ArtifactSeeder
 */
class ArtifactSeeder extends Seeder
{
    public function run(): void
    {
        // ── Categories ───────────────────────────────────────────────────────
        // firstOrCreate so re-running never duplicates rows.
        $catWeapons   = Category::firstOrCreate(['name' => 'Armes historiques']);
        $catDocuments = Category::firstOrCreate(['name' => 'Documents militaires']);
        $catMedals    = Category::firstOrCreate(['name' => 'Médailles & distinctions']);

        // ════════════════════════════════════════════════════════════════════
        // ARTIFACT 001 — Sabre de l'Émir Abd el-Kader
        // QR code value: artifact-001
        // 3D model: DamagedHelmet.glb — a battle-worn military helmet from the
        //   official Khronos glTF sample library. Contextually appropriate for
        //   a weapons/military artefact. Replace with real sword GLB later.
        // ════════════════════════════════════════════════════════════════════
        $a1 = Artifact::updateOrCreate(
            ['qr_code' => 'artifact-001'],
            [
                'name'        => 'Sabre de l\'Émir Abd el-Kader',
                'floor'       => 1,
                'category_id' => $catWeapons->id,
                'has_3d_model'=> true,
            ]
        );

        $this->seedTranslations($a1->id, [
            'ar' => [
                'name' => 'سيف الأمير عبد القادر الجزائري',
                'desc' => 'سيف تاريخي يعود إلى القرن التاسع عشر، استخدمه الأمير عبد القادر الجزائري في معاركه الشجاعة ضد الاستعمار الفرنسي بين عامَي 1832 و1847. يتميز بنقوش عربية دقيقة على النصل تتضمن آيات قرآنية، وقبضة من العاج المزيّن بالذهب. يُعدّ هذا السيف رمزاً خالداً للمقاومة والكرامة الوطنية الجزائرية.',
            ],
            'fr' => [
                'name' => 'Sabre de l\'Émir Abd el-Kader',
                'desc' => 'Sabre historique du XIXe siècle utilisé par l\'Émir Abd el-Kader al-Djazaïri dans ses combats contre la colonisation française entre 1832 et 1847. Il présente de fines inscriptions arabes sur la lame incluant des versets coraniques, et une poignée en ivoire incrustée d\'or. Ce sabre est un symbole éternel de résistance et de dignité nationale algérienne.',
            ],
            'en' => [
                'name' => 'Sword of Emir Abd el-Kader',
                'desc' => 'A 19th-century historical sword used by Emir Abd el-Kader al-Jazairi in his battles against French colonisation between 1832 and 1847. It features fine Arabic inscriptions on the blade including Quranic verses, and a gold-inlaid ivory handle. This sword is an eternal symbol of Algerian national resistance and dignity.',
            ],
            'es' => [
                'name' => 'Sable del Emir Abd el-Qader',
                'desc' => 'Sable histórico del siglo XIX utilizado por el Emir Abd el-Qader al-Argelino en sus batallas contra la colonización francesa entre 1832 y 1847. Presenta delicadas inscripciones árabes en la hoja que incluyen versículos coránicos y una empuñadura de marfil con incrustaciones de oro.',
            ],
            'zh' => [
                'name' => '埃米尔·阿卜杜·卡迪尔之剑',
                'desc' => '这是一把19世纪的历史名剑，由阿尔及利亚埃米尔·阿卜杜勒·卡迪尔在1832年至1847年间抗击法国殖民统治的战役中使用。剑身刻有精细的阿拉伯铭文，包括《古兰经》经文，手柄由镶金象牙制成。',
            ],
            'ru' => [
                'name' => 'Сабля эмира Абд аль-Кадира',
                'desc' => 'Историческая сабля XIX века, которую эмир Абд аль-Кадир аль-Джазаири использовал в боях против французской колонизации в 1832–1847 годах. На клинке выгравированы арабские надписи с кораническими аятами, рукоять из слоновой кости с золотыми вставками.',
            ],
        ]);

        // 3D model — military helmet from Khronos sample assets (public domain)
        ArtifactMedia::updateOrCreate(
            ['artifact_id' => $a1->id, 'type' => 'model_3d', 'is_main' => true],
            ['file_path' => 'https://raw.githubusercontent.com/KhronosGroup/glTF-Sample-Assets/main/Models/DamagedHelmet/glTF-Binary/DamagedHelmet.glb']
        );

        // Poster image (public domain via Wikimedia — Ottoman sword)
        ArtifactMedia::updateOrCreate(
            ['artifact_id' => $a1->id, 'type' => 'image', 'is_main' => true],
            ['file_path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/6b/Kilij_Arslan_II_sword.jpg/800px-Kilij_Arslan_II_sword.jpg']
        );


        // ════════════════════════════════════════════════════════════════════
        // ARTIFACT 002 — Proclamation du 1er Novembre 1954
        // QR code value: artifact-002
        // No 3D model — a document doesn't have geometry.
        // The visitor sees the image + description instead.
        // ════════════════════════════════════════════════════════════════════
        $a2 = Artifact::updateOrCreate(
            ['qr_code' => 'artifact-002'],
            [
                'name'        => 'Proclamation du 1er Novembre 1954',
                'floor'       => 1,
                'category_id' => $catDocuments->id,
                'has_3d_model'=> false,
            ]
        );

        $this->seedTranslations($a2->id, [
            'ar' => [
                'name' => 'وثيقة بيان أول نوفمبر 1954',
                'desc' => 'نسخة أصلية من بيان أول نوفمبر 1954، الذي أعلن فيه جبهة التحرير الوطني اندلاع الثورة الجزائرية المسلحة ضد الاستعمار الفرنسي. يُعدّ هذا البيان وثيقة تأسيسية في تاريخ الجزائر الحديث، ويمثل انطلاقة كفاح الشعب الجزائري من أجل الحرية والاستقلال الذي تحقق عام 1962.',
            ],
            'fr' => [
                'name' => 'Proclamation du 1er Novembre 1954',
                'desc' => 'Exemplaire original de la proclamation du 1er novembre 1954, par laquelle le Front de Libération Nationale a déclaré le déclenchement de la Révolution algérienne armée contre le colonialisme français. Ce document fondateur marque l\'entrée de l\'Algérie dans la lutte pour son indépendance, acquise en 1962.',
            ],
            'en' => [
                'name' => 'Proclamation of November 1st, 1954',
                'desc' => 'An original copy of the November 1st, 1954 proclamation, in which the National Liberation Front announced the start of the armed Algerian Revolution against French colonialism. This founding document marks Algeria\'s entry into the struggle for independence, achieved in 1962.',
            ],
            'es' => [
                'name' => 'Proclamación del 1 de Noviembre de 1954',
                'desc' => 'Copia original de la proclamación del 1 de noviembre de 1954, en la que el Frente de Liberación Nacional anunció el inicio de la Revolución argelina armada contra el colonialismo francés. Este documento fundacional marca la entrada de Argelia en la lucha por la independencia.',
            ],
            'zh' => [
                'name' => '1954年11月1日宣言文件',
                'desc' => '这是1954年11月1日宣言的原件，民族解放阵线在此宣告了阿尔及利亚武装革命对抗法国殖民主义的开始。这份具有奠基意义的文件标志着阿尔及利亚人民争取独立斗争的开始，最终于1962年取得独立。',
            ],
            'ru' => [
                'name' => 'Прокламация 1 ноября 1954 года',
                'desc' => 'Оригинальный экземпляр прокламации от 1 ноября 1954 года, в которой Фронт национального освобождения объявил о начале вооружённой Алжирской революции против французского колониализма. Этот основополагающий документ ознаменовал вступление Алжира в борьбу за независимость.',
            ],
        ]);

        ArtifactMedia::updateOrCreate(
            ['artifact_id' => $a2->id, 'type' => 'image', 'is_main' => true],
            ['file_path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e3/Proclamation_1_November_1954.jpg/800px-Proclamation_1_November_1954.jpg']
        );


        // ════════════════════════════════════════════════════════════════════
        // ARTIFACT 003 — Médaille du Moudjahid
        // QR code value: artifact-003
        // 3D model: MaterialsVariantsShoe.glb — a shiny metallic object from
        //   the Khronos library, closest available free model to a medal/coin.
        //   Replace with a real medal GLB when available.
        // ════════════════════════════════════════════════════════════════════
        $a3 = Artifact::updateOrCreate(
            ['qr_code' => 'artifact-003'],
            [
                'name'        => 'Médaille du Moudjahid',
                'floor'       => 2,
                'category_id' => $catMedals->id,
                'has_3d_model'=> true,
            ]
        );

        $this->seedTranslations($a3->id, [
            'ar' => [
                'name' => 'ميدالية المجاهد',
                'desc' => 'ميدالية شرفية أُسِّست عام 1963 تُمنح لكل من شارك في الكفاح المسلح من أجل استقلال الجزائر بين عامَي 1954 و1962. تحمل نجمة خماسية ذهبية اللون مع هلال، وشريطاً أخضر وأبيض يرمز إلى ألوان العلم الجزائري. تُعدّ هذه الميدالية أرفع وسام يمكن منحه للمحاربين القدامى.',
            ],
            'fr' => [
                'name' => 'Médaille du Moudjahid',
                'desc' => 'Décoration honorifique fondée en 1963, attribuée à toute personne ayant participé à la lutte armée pour l\'indépendance de l\'Algérie entre 1954 et 1962. Elle arbore une étoile à cinq branches dorée avec un croissant, sur un ruban vert et blanc aux couleurs du drapeau algérien. Elle constitue la plus haute distinction accordée aux anciens combattants.',
            ],
            'en' => [
                'name' => 'Medal of the Moudjahid',
                'desc' => 'An honorary decoration founded in 1963, awarded to those who participated in the armed struggle for Algerian independence between 1954 and 1962. It features a gold five-pointed star with a crescent, on a green and white ribbon representing the Algerian flag. It is the highest honour awarded to veterans of the Revolution.',
            ],
            'es' => [
                'name' => 'Medalla del Moudjahid',
                'desc' => 'Distinción honorífica fundada en 1963, otorgada a quienes participaron en la lucha armada por la independencia de Argelia entre 1954 y 1962. Presenta una estrella dorada de cinco puntas con una media luna, en una cinta verde y blanca con los colores de la bandera argelina.',
            ],
            'zh' => [
                'name' => '圣战者勋章',
                'desc' => '这枚荣誉勋章创立于1963年，授予1954年至1962年间参加阿尔及利亚独立武装斗争的人士。勋章正面为金色五角星配新月图案，绶带为绿白两色，代表阿尔及利亚国旗色彩。这是授予革命老战士的最高荣誉。',
            ],
            'ru' => [
                'name' => 'Медаль Муджахида',
                'desc' => 'Почётная награда, основанная в 1963 году, вручаемая участникам вооружённой борьбы за независимость Алжира в 1954–1962 годах. На медали изображена золотая пятиконечная звезда с полумесяцем на зелёно-белой ленте цветов алжирского флага.',
            ],
        ]);

        // 3D model — metallic object from Khronos (closest to a coin/medal)
        ArtifactMedia::updateOrCreate(
            ['artifact_id' => $a3->id, 'type' => 'model_3d', 'is_main' => true],
            ['file_path' => 'https://raw.githubusercontent.com/KhronosGroup/glTF-Sample-Assets/main/Models/IridescenceLamp/glTF-Binary/IridescenceLamp.glb']
        );

        ArtifactMedia::updateOrCreate(
            ['artifact_id' => $a3->id, 'type' => 'image', 'is_main' => true],
            ['file_path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8b/Medal_FLN.jpg/800px-Medal_FLN.jpg']
        );

        $this->command->info('✅  3 artifacts seeded successfully.');
        $this->command->info('    QR codes to test: artifact-001  artifact-002  artifact-003');
        $this->command->info('    Generate QR codes at: https://www.qr-code-generator.com/ (choose "Text" type)');
    }

    /**
     * Create or update all language translations for a given artifact.
     * updateOrCreate ensures re-running the seeder never creates duplicates.
     */
    private function seedTranslations(int $artifactId, array $langs): void
    {
        foreach ($langs as $locale => $data) {
            Translation::updateOrCreate(
                ['artifact_id' => $artifactId, 'locale' => $locale],
                ['name' => $data['name'], 'description' => $data['desc']]
            );
        }
    }
}
