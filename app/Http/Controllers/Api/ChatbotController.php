<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatbotLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatbotController extends Controller
{
    public function ask(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:1000',
            'language' => 'required|string',
        ]);

        $question = $request->question;
        $language = $request->language;
        $context  = $request->context ?? '';

        // Build multilingual system prompt
        $systemPrompt = $this->buildSystemPrompt($language, $context);

        // Call OpenAI API if key is configured
        $openaiKey = config('services.openai.key', env('OPENAI_API_KEY'));
        $answer    = null;

        if (!empty($openaiKey)) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $openaiKey,
                    'Content-Type'  => 'application/json',
                ])->timeout(25)->post('https://api.openai.com/v1/chat/completions', [
                    'model'       => 'gpt-4o-mini',
                    'max_tokens'  => 500,
                    'temperature' => 0.7,
                    'messages'    => [
                        [
                            'role'    => 'system',
                            'content' => $systemPrompt,
                        ],
                        [
                            'role'    => 'user',
                            'content' => $question,
                        ],
                    ],
                ]);

                if ($response->successful()) {
                    $data   = $response->json();
                    $answer = $data['choices'][0]['message']['content'] ?? null;
                }
            } catch (\Exception $e) {
                // Fall through to keyword fallback
                \Log::warning('OpenAI chatbot error: ' . $e->getMessage());
            }
        }

        // Fallback: keyword-based responses if OpenAI is unavailable
        if (empty($answer)) {
            $answer = $this->getKeywordResponse(strtolower($question), $language);
        }

        // Log the conversation
        try {
            ChatbotLog::create([
                'question' => $question,
                'response' => $answer,
                'language' => $language,
            ]);
        } catch (\Exception $e) {
            // Logging failure should not block response
        }

        return response()->json([
            'question' => $question,
            'answer'   => $answer,
        ]);
    }

    // ── System prompt ────────────────────────────────────────
    private function buildSystemPrompt(string $language, string $context): string
    {
        $langInstructions = match ($language) {
            'ar' => 'أجب دائماً باللغة العربية. كن مختصراً وودوداً.',
            'en' => 'Always respond in English. Be concise and friendly.',
            'es' => 'Responde siempre en español. Sé conciso y amable.',
            'ru' => 'Всегда отвечай на русском языке. Будь кратким и дружелюбным.',
            'zh' => '始终用中文回答。简明友善。',
            'it' => 'Rispondi sempre in italiano. Sii conciso e cordiale.',
            default => 'Réponds toujours en français. Sois concis et aimable.',
        };

        $base = "Tu es un guide historique expert du Musée Central de l'Armée Nationale Populaire d'Algérie (المتحف المركزي للجيش). "
            . "Tu as une connaissance approfondie de l'histoire militaire algérienne, de la guerre d'indépendance (1954-1962), "
            . "des figures historiques comme l'Émir Abdelkader, Houari Boumédiène, Larbi Ben M'hidi, "
            . "et des collections du musée incluant armes historiques, médailles, documents militaires, antiquités et artefacts culturels. "
            . "Tu aides les visiteurs à comprendre les pièces exposées et l'histoire qu'elles représentent. "
            . "Tes réponses doivent être informatives, respectueuses et adaptées à un public de musée. "
            . $langInstructions;

        if (!empty($context)) {
            $base .= "\n\nContexte de l'artefact actuellement consulté par le visiteur: " . $context;
        }

        return $base;
    }

    // ── Keyword fallback (multilingual) ──────────────────────
    private function getKeywordResponse(string $question, string $language): string
    {
        $keywords = [
            'horaire|heure|ouvre|ferme|opening|hours|horario|часы|时间' => [
                'fr' => 'Le musée est ouvert du dimanche au jeudi de 9h à 17h. Fermé le vendredi.',
                'ar' => 'المتحف مفتوح من الأحد إلى الخميس من الساعة 9:00 إلى 17:00. مغلق يوم الجمعة.',
                'en' => 'The museum is open Sunday to Thursday, 9am to 5pm. Closed on Fridays.',
                'es' => 'El museo está abierto de domingo a jueves, de 9h a 17h. Cerrado los viernes.',
                'ru' => 'Музей открыт с воскресенья по четверг с 9:00 до 17:00. Закрыт по пятницам.',
                'zh' => '博物馆周日至周四开放，9:00-17:00。周五关闭。',
                'it' => 'Il museo è aperto da domenica a giovedì, dalle 9 alle 17. Chiuso il venerdì.',
            ],
            'billet|ticket|prix|tarif|entrée|price|entrada|billete|цена|票价' => [
                'fr' => 'L\'entrée est gratuite pour tous les visiteurs. Bienvenue au Musée Central de l\'Armée !',
                'ar' => 'الدخول مجاني لجميع الزوار. أهلاً بكم في المتحف المركزي للجيش!',
                'en' => 'Admission is free for all visitors. Welcome to the Central Army Museum!',
                'es' => 'La entrada es gratuita para todos los visitantes.',
                'ru' => 'Вход свободный для всех посетителей.',
                'zh' => '所有参观者免费入场。',
                'it' => 'L\'ingresso è gratuito per tutti i visitatori.',
            ],
            'révolution|indépendance|1954|1962|independence|revolution|революция|革命' => [
                'fr' => 'La Révolution algérienne (1954-1962) est au cœur des collections du musée. Le 1er novembre 1954 marque le déclenchement de la lutte armée contre la colonisation française. Notre collection présente des documents, uniformes et armes de cette période historique.',
                'ar' => 'الثورة الجزائرية (1954-1962) هي محور مجموعات المتحف. يمثل الأول من نوفمبر 1954 بداية الكفاح المسلح ضد الاستعمار الفرنسي.',
                'en' => 'The Algerian Revolution (1954-1962) is at the heart of the museum\'s collections. November 1st, 1954 marks the start of the armed struggle against French colonialism. Our collection features documents, uniforms, and weapons from this historic period.',
                'es' => 'La Revolución Argelina (1954-1962) es el corazón de las colecciones del museo.',
                'ru' => 'Алжирская революция (1954-1962) — в центре коллекций музея.',
                'zh' => '阿尔及利亚革命（1954-1962年）是博物馆藏品的核心。',
                'it' => 'La Rivoluzione Algerina (1954-1962) è al centro delle collezioni del museo.',
            ],
            'photo|appareil|camera|fotografía|фото|照片' => [
                'fr' => 'Les photographies sont autorisées dans toutes les salles sans flash. Nous vous encourageons à partager votre visite!',
                'ar' => 'التصوير مسموح به في جميع القاعات بدون فلاش.',
                'en' => 'Photography is allowed in all rooms without flash.',
                'es' => 'Se permite fotografiar en todas las salas sin flash.',
                'ru' => 'Фотосъёмка разрешена во всех залах без вспышки.',
                'zh' => '所有展厅均可拍照，但不得使用闪光灯。',
                'it' => 'Le fotografie sono consentite in tutte le sale senza flash.',
            ],
        ];

        foreach ($keywords as $pattern => $translations) {
            if (preg_match('/(' . $pattern . ')/i', $question)) {
                return $translations[$language] ?? $translations['fr'];
            }
        }

        // Generic fallback per language
        return match ($language) {
            'ar' => 'شكراً على سؤالك! هذا المتحف يحتضن مجموعة غنية من التاريخ العسكري الجزائري. يمكنك التجول في أرجائه أو سؤال أحد المرشدين للمزيد من المعلومات.',
            'en' => 'Thank you for your question! This museum holds a rich collection of Algerian military history. Feel free to explore or ask one of our guides for more information.',
            'es' => 'Gracias por su pregunta. Este museo alberga una rica colección de historia militar argelina.',
            'ru' => 'Спасибо за вопрос! Этот музей хранит богатую коллекцию алжирской военной истории.',
            'zh' => '感谢您的提问！该博物馆拥有丰富的阿尔及利亚军事历史藏品。',
            'it' => 'Grazie per la domanda! Questo museo conserva una ricca collezione di storia militare algerina.',
            default => 'Merci pour votre question ! Ce musée abrite une riche collection d\'histoire militaire algérienne. N\'hésitez pas à explorer les salles ou à interroger l\'un de nos guides.',
        };
    }
}
