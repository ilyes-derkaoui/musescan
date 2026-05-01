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
        }

        HistoricalFigure::updateOrCreate(
            ['name' => 'Colonel Amirouche'],
            ['birth_year' => 1926, 'death_year' => 1959, 'artifact_id' => Artifact::where('qr_code', 'artifact-003')->value('id')]
        );
    }
}
