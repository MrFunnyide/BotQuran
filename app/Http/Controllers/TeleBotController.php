<?php

namespace App\Http\Controllers;

use App\Models\Chapters;
use App\Models\Verses;
use App\Models\verses_translations;
use App\Models\word_translations;
use App\Models\word_verses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Telegram\Bot\Api;

class TeleBotController extends Controller
{
    protected $telegram;
    
    const QS_GUIDE = "<strong>Welcome To The Quran Type</strong>".PHP_EOL
    ."QuranType adalah sebuah bot yang bisa menampilkan Ayat Al-Quran sesuai dengan apa yang anda Typing."
    .PHP_EOL.PHP_EOL
    ."Ada Beberapa Command atau Perintah yang bisa anda lakukan, seperti :"
    .PHP_EOL
    ."<strong>Quran By Number : </strong>"
    .'{'.'/qn'.'}'
    .PHP_EOL
    ."<strong>Quran By View : </strong>"
    .'{'.'/qv'.'}'
    .PHP_EOL
    ."<strong>Quran By Translations : </strong>"
    .'{'.'/qtc'.'}'
    .PHP_EOL
    ."<strong>Quran Word By Word : </strong>"
    .'{'.'/qwbw'.'}'
    .PHP_EOL
    ."<strong>Quran View Word By Word : </strong>"
    .'{'.'/qvwbw'.'}'
    .PHP_EOL
    ."<strong>Quran Translate Word By Word : </strong>"
    .'{'.'/qtwbw'.'}'
    .PHP_EOL
    ."<strong>Quran Search : </strong>"
    .'{'.'/qs'.'}';

    const QN_GUIDE = "<strong>QN</strong>".PHP_EOL
    ."Menu ini untuk menampilkan Surat dan Ayat Al-Quran."
    .PHP_EOL.PHP_EOL
    ."Cara menggunakan:"
    .PHP_EOL
    ."<code>Silahkan  mengetik Surat dan Ayat yang dicari."
    .PHP_EOL
    ."/qn [surat]:[ayat]</code>"
    .PHP_EOL.PHP_EOL
    ."Contoh:"
    .PHP_EOL
    ."<code>/qn 1:1</code>"
    .PHP_EOL
    ."<code>/qn 1:1-3</code>";

    const QN_WBW = "<strong>QWBW</strong>".PHP_EOL
    ."Menu ini untuk menampilkan Kata perkata pada ayat Al-Quran."
    .PHP_EOL.PHP_EOL
    ."Cara menggunakan:"
    .PHP_EOL
    ."<code>Silahkan  mengetik Surat dan Ayat yang dicari."
    .PHP_EOL
    ."/qwbw [surat]:[ayat]</code>"
    .PHP_EOL.PHP_EOL
    ."Contoh:"
    .PHP_EOL
    ."<code>/qwbw 1:1</code>";

    const QT_WBW = "<strong>QTWBW</strong>".PHP_EOL
    ."Menu ini untuk menampilkan Translate Kata perkata pada ayat Al-Quran."
    .PHP_EOL.PHP_EOL
    ."Daftar Translator bisa di lihati di bawah ini:"
    .PHP_EOL.
    "<a href='https://ade3-182-1-0-38.ap.ngrok.io/daftarLanguage'>QuranType</a>"
    .PHP_EOL.PHP_EOL
    ."Cara menggunakan:"
    .PHP_EOL
    ."<code>Silahkan  mengetik Surat, Ayat, kata keberapa, id bahasa  yang dicari."
    .PHP_EOL
    ."/qtwbw [surat]:[ayat]:[nomor]:[id bahasa]</code>"
    .PHP_EOL.PHP_EOL
    ."Contoh:"
    .PHP_EOL
    ."<code>/qtwbw 1:1:1:1</code>";

    const QV_WBW = "<strong>QVWBW</strong>".PHP_EOL
    ."Menu ini untuk menampilkan View Kata perkata pada ayat Al-Quran."
    .PHP_EOL.PHP_EOL
    ."Cara menggunakan:"
    .PHP_EOL
    ."<code>Silahkan  mengetik Surat, Ayat, Kata keberapa yang dicari."
    .PHP_EOL
    ."/qwbw [surat]:[ayat]:[nomor]</code>"
    .PHP_EOL.PHP_EOL
    ."Contoh:"
    .PHP_EOL
    ."<code>/qvwbw 1:1:1</code>";

    const QV_GUIDE = "<strong>QV</strong>".PHP_EOL
    ."Menu ini untuk menampilkan salah satu Ayat Al-Quran beserta terjemahan)."
    .PHP_EOL.PHP_EOL
    ."Cara menggunakan:"
    .PHP_EOL
    ."<code>Silahkan  mengetik Surat dan Ayat yang dicari."
    .PHP_EOL
    ."/qv [surat]:[ayat]</code>"
    .PHP_EOL.PHP_EOL
    ."Contoh:"
    .PHP_EOL
    ."<code>/qv 1:1</code>";

    const Qcari_GUIDE = "<strong>QS Pencarian</strong>".PHP_EOL
    ."Menu ini untuk menampilkan Pencarian Terjemahan Alquran"
    .PHP_EOL.
    "Ada beberapa cara untuk mencari terjemahan, yakni : "
    .PHP_EOL
    ."1.berdasarkan kata /qsword"
    .PHP_EOL
    ."2.berdasarkan lebih dari satu kata /qstrans"
    .PHP_EOL
    ."Cara Menggunakan : "
    .PHP_EOL
    .PHP_EOL
    ."<code>/qsword sapi</code>"
    .PHP_EOL
    ."<code>/qstrans halangan bagi orang buta</code>";


    const QTrans_GUIDE = "<strong>QTranslations</strong>".PHP_EOL
    ."Menu ini untuk menampilkan Terjemahan Al-Quran dari berbagai Translator"
    .PHP_EOL.PHP_EOL
    ."Daftar Translator bisa di lihati di bawah ini:"
    .PHP_EOL.
    "<a href='https://ade3-182-1-0-38.ap.ngrok.io/daftarTranslator'>QuranType</a>"
    .PHP_EOL.PHP_EOL
    ."Cara menggunakan:"
    .PHP_EOL
    ."<code>Silahkan  mengetik Qori dan Ayat yang dicari."
    .PHP_EOL
    ."/qtc [Translator]:[surah]:[ayat]</code>"
    .PHP_EOL.PHP_EOL
    ."Contoh:"
    .PHP_EOL
    ."<code>/qtc 17:1:1</code>";

    public function __construct() {
        $this->telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
    }

    public function sendMessage($id) {
        return $this->telegram->sendMessage([
            'chat_id' => $id,
            'text' => 'Assalamualaikum'
        ]);
    }
    public function messages() {
        return $this->telegram->getUpdates();
    }

    public function setWebhook() {

        try {
            $url = env('NGROK_URL');
            $this->telegram->setWebhook([
                'url' => $url.'/api/telegram/webhook/'.env('TELEGRAM_BOT_TOKEN')
            ]);
            return ['message' => 'Set webhook is already to use'];
        } catch (\Exception $exception) {
            return ['message' => "ERROR $exception"];
        }
    }

    public function webhook(Request $request) {

        try {
            //mengambil ID User
            $userId = $request['message']['from']['id'];
            //Pesan
            $input = $request['message']['text'];

            //Pecah kalau ada spasi
            $chat = explode(" ", $input);
            $chat2 = explode("_", $input);

            if ($chat[0] == "/qn") {
                if (sizeof($chat) <= 1) {
                    //pesan Menu QN
                    $message = self::QN_GUIDE;
                    $this->telegram->sendMessage([
                        'parse_mode'=>'HTML',
                        'chat_id' => $userId,
                        'text' => $message
                    ]);
                    //mengambil surah dan ayat
                } elseif(sizeof($chat) == 2) {
                    $verse_key = $chat[1];
                    //memisah surah dan ayat
                    $pecah = explode(":", $verse_key);
                    $idChapter = $pecah[0];
                    $verseNumber = $pecah[1];
                    if ($idChapter <= 114 && $idChapter >= 1) {
                        //jika ayat memiliki rentang yang diinginkan
                        $pecahVerseNumber = explode("-", $verseNumber);
                        if (sizeof($pecahVerseNumber) == 1) {
                            $firstVerses = $pecahVerseNumber[0];
                            //mengihutung banyak ayat di dalam surah
                            $count = Verses::where('id_chapter', $idChapter)->count();
                            if ($firstVerses <= $count) {
                                $chapters = Chapters::query()->where('id', $idChapter)->firstOrFail();
                                $verses = Verses::query()->where('id_chapter', $idChapter)->where('number', $firstVerses)->get();
                                $messageOut = self::getQNAnswere($verses, $chapters);
                                $this->telegram->sendMessage([
                                    'parse_mode'=>'HTML',
                                    'chat_id' => $userId,
                                    'text' => $messageOut
                                ]);
                            } else {
                                $this->telegram->sendMessage([
                                    'parse_mode'=>'HTML',
                                    'chat_id' => $userId,
                                    'text' => "Verses more than $count"
                                ]);
                            }
                            //terdapat rentang ayat
                        } elseif (sizeof($pecahVerseNumber) == 2) {
                            $firstVerses = $pecahVerseNumber[0];
                            $lastVerses = $pecahVerseNumber[1];
                            $versesBetween = Verses::query()
                                ->where('id_chapter', $idChapter)->whereBetween('number', [$firstVerses,$lastVerses])->get();
                            $chapters = Chapters::query()->where('id', $idChapter)->firstOrFail();
                            //ambil text dengan fungsi
                            $messageOut = self::getQNAnswere($versesBetween, $chapters);
                            $splitVerse = str_split($messageOut, 4090);
                            foreach ($splitVerse as $value) {
                                try {
                                    $this->telegram->sendMessage([
                                        'parse_mode'=>'HTML',
                                        'chat_id' => $userId,
                                        'text' => htmlspecialchars($value)
                                    ]);
                                } catch (\Exception $e) {
                                    $this->telegram->sendMessage([
                                        'parse_mode'=>'HTML',
                                        'chat_id' => $userId,
                                        'text' => "Error bagian qn"
                                    ]);
                                }
                            }
                        } else {
                            $this->telegram->sendMessage([
                                'parse_mode'=>'HTML',
                                'chat_id' => $userId,
                                'text' => "Ayat tidak ditemukan"
                            ]);
                        }
                    } else {
                        $this->telegram->sendMessage([
                            'parse_mode'=>'HTML',
                            'chat_id' => $userId,
                            'text' => "Surah tidak di temukan"
                        ]);
                    }

                } else {
                    $this->telegram->sendMessage([
                        'parse_mode'=>'HTML',
                        'chat_id' => $userId,
                        'text' => "Keyword Error"
                    ]);
                }
            } elseif ($chat[0] == "/start") {
                $messageIn = self::QS_GUIDE;
                $this->telegram->sendMessage([
                    'parse_mode'=>'HTML',
                    'chat_id' => $userId,
                    'text' =>  $messageIn
                ]);

            } elseif ($chat[0] == "/qv") { // langsung perayat
                if(sizeof($chat) <= 1) {
                    $messageOut = self::QV_GUIDE;
                    $this->telegram->sendMessage([
                        'parse_mode'=>'HTML',
                        'chat_id' => $userId,
                        'text' => $messageOut,
                    ]);
                } elseif (sizeof($chat) == 2) {
                    // code...
                    // dapat kan nomr surat dan nomor ayat
                    $noChapAndNoVers = explode(":", $chat[1]);
                    if (sizeof($noChapAndNoVers) != 2 ) {
                        throw new  \Exception("Format yang anda masukkan salah.", -1);
                    }

                    if ($noChapAndNoVers[0] <= 114 && $noChapAndNoVers[0] >= 1) {
                        // ini isi chapter dan ayat
                    $surah = Chapters::where('id', $noChapAndNoVers[0])->first();
                    $countayat = Verses::query()->where('id_chapter', $surah->id)->count();

                        if ($noChapAndNoVers[1] <= $countayat) {
                            $ayat = Verses::query()->where('id_chapter', $surah->id)->where('number', $noChapAndNoVers[1])->first();
                            $messageOut = self::getQVAnswere($ayat, $surah);
                            $this->telegram->sendMessage([
                                'parse_mode'=>'HTML',
                                'chat_id' => $userId,
                                'text' => $messageOut
                            ]);    
                        } else {
                            $this->telegram->sendMessage([
                                'parse_mode'=>'HTML',
                                'chat_id' => $userId,
                                'text' => "Ayat tidak di temukan"
                            ]);    
                        }
                    } else {
                        $this->telegram->sendMessage([
                            'parse_mode'=>'HTML',
                            'chat_id' => $userId,
                            'text' => "Surah tidak di temukan"
                        ]);
                    }
                } else {
                    $this->telegram->sendMessage([
                        'parse_mode'=>'HTML',
                        'chat_id' => $userId,
                        'text' => "Keyword yang anda masukan salah, silahkan masukkan keyword yang benar."
                    ]);
                }

            } elseif ($chat2[0] == "/qv") {
                if (sizeof($chat2)<= 1) {
                    $messageOut = self::QV_GUIDE;
                    $this->telegram->sendMessage([
                        'parse_mode'=>'HTML',
                        'chat_id' => $userId,
                        'text' => $messageOut,
                    ]);
                } elseif(sizeof($chat2) == 3) {
                    // isi chapter dan ayat , sama dengan yang di atas
                    if ($chat2[1] <= 114 && $chat2 >= 1) {
                        $surah = Chapters::where('id', $chat2[1])->first();
                        $countayat = Verses::query()->where('id_chapter', $surah->id)->count();

                        if ($chat2[2] <= $countayat) {
                            $ayat = Verses::query()->where('id_chapter', $surah->id)->where('number', $chat2[2])->first();

                            $messageOut = self::getQVAnswere($ayat, $surah);
                            $this->telegram->sendMessage([
                                'parse_mode'=>'HTML',
                                'chat_id' => $userId,
                                'text' => $messageOut
                            ]);
                        } else {
                            $this->telegram->sendMessage([
                                'parse_mode'=>'HTML',
                                'chat_id' => $userId,
                                'text' => "Ayat tidak ditemukan"
                            ]);
                        }
                    } else {
                        $this->telegram->sendMessage([
                            'parse_mode' => 'HTML',
                            'chat_id' => $userId,
                            'text' => "Surah tidak di temukan"
                        ]);
                    }
                } else {
                    $this->telegram->sendMessage([
                        'parse_mode'=>'HTML',
                        'chat_id' => $userId,
                        'text' => "Keyword yang anda masukan salah, silahkan masukkan keyword yang benar."
                    ]);
                }
//                $chat2 = explode("_", $input);
            } elseif ($chat[0] == '/qtc') {
                if (sizeof($chat) <= 1) {
                    $messageOut = self::QTrans_GUIDE;
                    $this->telegram->sendMessage([
                        'parse_mode' => 'HTML',
                        'chat_id' => $userId,
                        'text' => $messageOut,
                    ]);
                } elseif (sizeof($chat) == 2) {
                    $noTransSurahAyah = explode(":", $chat[1]);
                    if (sizeof($noTransSurahAyah) > 3 || sizeof($noTransSurahAyah) < 3 ) {
                        $this->telegram->sendMessage([
                            'parse_mode' => 'HTML',
                            'chat_id' => $userId,
                            'text' => "Format yang anda masukkan salah "
                        ]); 
                    } else {
                        if ($noTransSurahAyah[1] <= 114 && $noTransSurahAyah[1] >= 1) {
                            $countayat = Verses::query()->where('id_chapter', $noTransSurahAyah[1])->count();

                            if ($noTransSurahAyah[2] <= $countayat) {
                                $find = DB::table('verse_translations')
                                ->join('verses', 'verse_translations.id_verse', '=', 'verses.id')
                                ->join('translations', 'verse_translations.id_translation', '=', 'translations.id')
                                ->where('translations.id', $noTransSurahAyah[0])
                                ->where('verses.id_chapter', $noTransSurahAyah[1])
                                ->where('verses.number', $noTransSurahAyah[2])
                                ->get();

                                $messageOut = self::getTRC($find);
                                $this->telegram->sendMessage([
                                    'parse_mode' => 'HTML',
                                    'chat_id' => $userId,
                                    'text' => $messageOut
                                ]);
                            } else {
                                $this->telegram->sendMessage([
                                    'parse_mode' => 'HTML',
                                    'chat_id' => $userId,
                                    'text' => "Ayat tidak ditemukan"
                                ]);
                            }
                        } else {
                            $this->telegram->sendMessage([
                                'parse_mode' => 'HTML',
                                'chat_id' => $userId,
                                'text' => "Surah tidak ditemukan"
                            ]);
                        }
                    } 
                }   
            } elseif ($chat2[0] == '/qtc') {
                if (sizeof($chat2) <= 1) {
                    $messageOut = self::QTrans_GUIDE;
                    $this->telegram->sendMessage([
                        'parse_mode' => 'HTML',
                        'chat_id' => $userId,
                        'text' => $messageOut,
                    ]);
                } elseif (sizeof($chat2) == 4) {
                    // dapatkan nomor translator dengan id chapter

                    if ($chat2[2] <= 114 && $chat2 >= 1) {
                        // code...

                        $countayat = Verses::query()->where('id_chapter', $chat2[2])->count();

                        if ($chat2[3] <= $countayat) {
                            $find = DB::table('verse_translations')
                            ->join('verses', 'verse_translations.id_verse', '=', 'verses.id')
                            ->join('translations', 'verse_translations.id_translation', '=', 'translations.id')
                            ->where('translations.id', $chat2[1])
                            ->where('verses.id_chapter', $chat2[2])
                            ->where('verses.number', $chat2[3])
                            ->get();

                            $messageOut = self::getTRC($find);
                            $this->telegram->sendMessage([
                                'parse_mode' => 'HTML',
                                'chat_id' => $userId,
                                'text' => $messageOut
                            ]);
                        } else {
                            $this->telegram->sendMessage([
                                'parse_mode' => 'HTML',
                                'chat_id' => $userId,
                                'text' => "Ayat tidak ditemukan"
                            ]);
                        }
                    } else {
                        $this->telegram->sendMessage([
                            'parse_mode' => 'HTML',
                            'chat_id' => $userId,
                            'text' => "Surah tidak ditemukan"
                        ]);
                    }
                } else {
                    $this->telegram->sendMessage([
                        'parse_mode'=>'HTML',
                        'chat_id' => $userId,
                        'text' => "Keyword yang anda masukan salah, silahkan masukkan keyword yang benar."
                    ]);
                }
            } elseif ($chat[0] == "/qwbw") {
                if (sizeof($chat) <= 1) {
                    $messageOut = self::QN_WBW;
                    $this->telegram->sendMessage([
                        'parse_mode' => 'HTML',
                        'chat_id' => $userId,
                        'text' => $messageOut,
                    ]);
                } elseif(sizeof($chat) == 2) {
                    $noChapAndNoVers = explode(":", $chat[1]);
                    if (sizeof($noChapAndNoVers) != 2 ) {
                        throw new  \Exception("Format yang anda masukkan salah.", -1);
                    }

                    if ($noChapAndNoVers[0] <= 114 && $noChapAndNoVers[0] >= 1) {
                         // isi dengan ayat dan chapter
                        $surah = Chapters::where('id', $noChapAndNoVers[0])->first();
                        $countayat = Verses::query()->where('id_chapter', $surah->id)->count();

                        if ($noChapAndNoVers[1] <= $countayat) {
                            $ayat = Verses::where('id_chapter', $surah->id)->where('number', $noChapAndNoVers[1])->first();
                            $word = word_verses::where('id_verse', $ayat->id)->get();

                            $messageOut = self::getQwbwAnswere($word, $ayat);
                            $splitMessage = str_split($messageOut, 4090);
                            foreach ($splitMessage as $resultMess) {
                                try {
                                    $this->telegram->sendMessage([
                                        'parse_mode'=>'HTML',
                                        'chat_id' => $userId,
                                        'text' => htmlspecialchars($resultMess)
                                    ]);     
                                } catch (\Exception $e) {
                                    $this->telegram->sendMessage([
                                        'parse_mode'=>'HTML',
                                        'chat_id' => $userId,
                                        'text' => "Error Qwbw"
                                    ]);
                                }
                            }
                        } else {
                            $this->telegram->sendMessage([
                                'parse_mode'=>'HTML',
                                'chat_id' => $userId,
                                'text' => "Ayat tidak ditemukan"
                            ]);     
                        }
                    } else {
                        $this->telegram->sendMessage([
                            'parse_mode' => 'HTML',
                            'chat_id' => $userId,
                            'text' => "Surah tidak ditemukan"
                        ]);
                    }
                } else {
                    $this->telegram->sendMessage([
                        'parse_mode' => 'HTML',
                        'chat_id' => $userId,
                        'text' => "Keyword yang anda masukan salah, silahkan masukkan keyword yang benar."
                    ]);
                }
            } elseif ($chat2[0] == "/qwbw") {
                if (sizeof($chat2) <= 1) {
                    $messageOut = self::QN_WBW;
                    $this->telegram->sendMessage([
                        'parse_mode' => 'HTML',
                        'chat_id' => $userId,
                        'text' => $messageOut,
                    ]);
                } elseif (sizeof($chat2) == 3) {
                    // isi dengan ayat dan chapter

                    if ($chat2[1] <= 114 && $chat2[1] >= 1) {
                        $surah = Chapters::where('id', $chat2[1])->first();
                        $countayat = Verses::query()->where('id_chapter', $surah->id)->count();

                        if ($chat2[2] <= $countayat) {
                            $ayat = Verses::where('id_chapter', $surah->id)->where('number', $chat2[2])->first();
                            $word = word_verses::where('id_verse', $ayat->id)->get();

                            $messageOut = self::getQwbwAnswere($word, $ayat);
                            $splitMessage = str_split($messageOut, 4090);
                            foreach ($splitMessage as $resultMess) {
                                try {
                                    $this->telegram->sendMessage([
                                        'parse_mode'=>'HTML',
                                        'chat_id' => $userId,
                                        'text' => htmlspecialchars($resultMess)
                                    ]);     
                                } catch (\Exception $e) {
                                    $this->telegram->sendMessage([
                                        'parse_mode'=>'HTML',
                                        'chat_id' => $userId,
                                        'text' => error_log($e)
                                    ]);
                                }
                            }
                        } else {
                            $this->telegram->sendMessage([
                                'parse_mode'=>'HTML',
                                'chat_id' => $userId,
                                'text' => "Ayat tidak ditemukan"
                            ]);
                        }
                    } else {
                        $this->telegram->sendMessage([
                            'parse_mode'=>'HTML',
                            'chat_id' => $userId,
                            'text' => "Surah tidak di temukan."
                        ]);
                    }
                } else {
                    $this->telegram->sendMessage([
                        'parse_mode' => 'HTML',
                        'chat_id' => $userId,
                        'text' => "Keyword yang anda masukan salah, silahkan masukkan keyword yang benar."
                    ]);
                }
            } elseif ($chat[0] == "/qvwbw") {
                if (sizeof($chat) <= 1) {
                    $messageOut = self::QV_WBW;
                    $this->telegram->sendMessage([
                        'parse_mode' => 'HTML',
                        'chat_id' => $userId,
                        'text' => $messageOut,
                    ]);
                } elseif (sizeof($chat) == 2 ) {
                    $chaptAndVerse = explode(":", $chat[1]);
                    if (sizeof($chaptAndVerse) != 3 ) {
                        throw new  \Exception("Format yang anda masukkan salah.", -1);
                    }

                    if ($chaptAndVerse[0] <= 114 && $chaptAndVerse[0] >= 1) {
                        // isi dengan ayat dan chapter
                        $surah = Chapters::where('id', $chaptAndVerse[0])->first();
                        $countayat = Verses::query()->where('id_chapter', $surah->id)->count();

                        if ($chaptAndVerse[1] <= $countayat) {
                            $ayat = Verses::where('id_chapter', $surah->id)->where('number', $chaptAndVerse[1])->first();
                            $word = word_verses::where('id_verse', $ayat->id)->where('number', $chaptAndVerse[2])->first();

                            $messageOut = self::getQvwbwAnswere($word, $ayat, $surah);
                            $this->telegram->sendMessage([
                                'parse_mode'=>'HTML',
                                'chat_id' => $userId,
                                'text' => $messageOut
                            ]);    
                        } else {
                            $this->telegram->sendMessage([
                                'parse_mode'=>'HTML',
                                'chat_id' => $userId,
                                'text' => "Ayat tidak ditemukan"
                            ]);    
                        }
                    } else {
                        $this->telegram->sendMessage([
                            'parse_mode'=>'HTML',
                            'chat_id' => $userId,
                            'text' => "Surah tidak di temukan"
                        ]);
                    }                    
                } else {
                    $this->telegram->sendMessage([
                        'parse_mode' => 'HTML',
                        'chat_id' => $userId,
                        'text' => "Keyword yang anda masukan salah, silahkan masukkan keyword yang benar."
                    ]);
                }

            } elseif ($chat2[0] == "/qvwbw") {
                if (sizeof($chat2) <= 1) {
                    $messageOut = self::QV_WBW;
                    $this->telegram->sendMessage([
                        'parse_mode' => 'HTML',
                        'chat_id' => $userId,
                        'text' => $messageOut,
                    ]);
                } elseif(sizeof($chat2) == 4 ) {

                    if ($chat2[1] <= 114 && $chat2 >= 1) {
                        // isi dengan ayat dan chapter
                        $surah = Chapters::where('id', $chat2[1])->first();
                        $countayat = Verses::query()->where('id_chapter', $surah->id)->count();

                        if ($chat2[2] <= $countayat) {
                            $ayat = Verses::where('id_chapter', $surah->id)->where('number', $chat2[2])->first();
                            $word = word_verses::where('id_verse', $ayat->id)->where('number', $chat2[3])->first();

                            $messageOut = self::getQvwbwAnswere($word, $ayat, $surah);
                            $this->telegram->sendMessage([
                                'parse_mode' => 'HTML',
                                'chat_id' => $userId,
                                'text' => $messageOut
                            ]);
                        } else {
                            $this->telegram->sendMessage([
                                'parse_mode' => 'HTML',
                                'chat_id' => $userId,
                                'text' => "Ayat tidak ditemukan"
                            ]);
                        }
                    } else {
                        $this->telegram->sendMessage([
                            'parse_mode' => 'HTML',
                            'chat_id' => $userId,
                            'text' => 'Surah tidak ditemukan'
                        ]);
                    }
                } else {
                    $this->telegram->sendMessage([
                        'parse_mode' => 'HTML',
                        'chat_id' => $userId,
                        'text' => "Keyword yang anda masukan salah, silahkan masukkan keyword yang benar."
                    ]);
                }
            } elseif($chat[0] == "/qtwbw") {
                if (sizeof($chat) <= 1) {
                    $messageOut = self::QT_WBW;
                    $this->telegram->sendMessage([
                        'parse_mode' => 'HTML',
                        'chat_id' => $userId,
                        'text' => $messageOut
                    ]);
                } elseif(sizeof($chat) == 2) {
                    $space = explode(":", $chat[1]);
                    if (sizeof($space) != 4 ) {
                        throw new  \Exception("Format yang anda masukkan salah.", -1);
                    }

                    if ($space[0] <= 114 && $space[0] >= 1) {
                        $surah = Chapters::where('id', $space[0])->first();
                        $countayat = Verses::query()->where('id_chapter', $surah->id)->count();

                        if ($space[1] <= $countayat) {
                            $ayat = Verses::where('id_chapter', $surah->id)->where('number', $space[1])->first();
                            $word = word_verses::where('id_verse', $ayat->id)->where('number', $space[2])->first();
                            $word2 = word_translations::where('id_word_verse', $word->id)->where('id_language', $space[3])->first();

                            $messageIn = self::getQtwbwAnswere($word, $word2, $ayat);
                            $this->telegram->sendMessage([
                                'parse_mode'=>'HTML',
                                'chat_id' => $userId,
                                'text' => $messageIn
                            ]);
                        } else {
                            $this->telegram->sendMessage([
                                'parse_mode'=>'HTML',
                                'chat_id' => $userId,
                                'text' => "Ayat tidak ditemukan"
                            ]);
                        }
                    } else {
                        $this->telegram->sendMessage([
                            'parse_mode'=>'HTML',
                            'chat_id' => $userId,
                            'text' => "Surah tidak ditemukan"
                        ]);
                    }                    
                } else {
                    $this->telegram->sendMessage([
                        'parse_mode' => 'HTML',
                        'chat_id' => $userId,
                        'text' => "Keyword yang anda masukan salah, silahkan masukkan keyword yang benar."
                    ]);
                }

            } elseif ($chat2[0] == "/qtwbw") {
                if (sizeof($chat2) <= 1) {
                    $messageOut = self::QT_WBW;
                    $this->telegram->sendMessage([
                        'parse_mode' => 'HTML',
                        'chat_id' => $userId,
                        'text' => $messageOut
                    ]);
                } elseif (sizeof($chat2) == 5) {

                    if ($chat2[1] <= 114 && $chat2[1] >= 1) {
                        $surah = Chapters::where('id', $chat2[1])->first();
                        $countayat = Verses::query()->where('id_chapter', $surah->id)->count();

                        if ($chat2[2] <= $countayat) {
                            $ayat = Verses::where('id_chapter', $surah->id)->where('number', $chat2[2])->first();
                            $word = word_verses::where('id_verse', $ayat->id)->where('number', $chat2[3])->first();
                            $word2 = word_translations::where('id_word_verse', $word->id)->where('id_language', $chat2[4])->first();

                            $messageIn = self::getQtwbwAnswere($word, $word2, $ayat);
                            $this->telegram->sendMessage([
                                'parse_mode'=>'HTML',
                                'chat_id' => $userId,
                                'text' => $messageIn
                            ]);
                        } else {
                            $this->telegram->sendMessage([
                                'parse_mode'=>'HTML',
                                'chat_id' => $userId,
                                'text' => "Ayat tidak ditemukan"
                            ]);
                        }
                    } else {
                        $this->telegram->sendMessage([
                            'parse_mode'=>'HTML',
                            'chat_id' => $userId,
                            'text' => "Surah tidak ditemukan"
                        ]);
                    }
                } else {
                   $this->telegram->sendMessage([
                        'parse_mode' => 'HTML',
                        'chat_id' => $userId,
                        'text' => "Keyword yang anda masukan salah, silahkan masukkan keyword yang benar."
                    ]); 
                }
            } elseif($chat[0] == '/qs') {
                if (sizeof($chat) <=1 ) {
                    $messageOut = self::Qcari_GUIDE;
                    $this->telegram->sendMessage([
                        'parse_mode' => 'HTML',
                        'chat_id' => $userId,
                        'text' => $messageOut
                    ]);
                }
            } elseif ($chat[0] == '/qsword') {
                if (sizeof($chat) <=1 ) {
                    $messageOut = self::Qcari_GUIDE;
                    $this->telegram->sendMessage([
                        'parse_mode' => 'HTML',
                        'chat_id' => $userId,
                        'text' => $messageOut
                    ]);
                } elseif (sizeof($chat) == 2) {
                    $countAll = DB::table('word_translations')
                        ->join('word_verses', 'word_translations.id_word_verse', '=', 'word_verses.id' )
                        ->join('verses', 'word_verses.id_verse', '=', 'verses.id')
                        ->join('chapters', 'verses.id_chapter', '=', 'chapters.id')
                        ->where('word_translations.text', 'LIKE', '%'.$chat[1].'%')
                        ->where('word_translations.id_language', '=', 13)
                        ->get()->count();

                    if ($countAll == 0){
                        $messageOut = 'Terdapat <strong>'.$countAll.' ayat ditemukan</strong>, silahkan coba dengan kata kunci lain.';
                        $this->telegram->sendMessage([
                            'parse_mode'=>'HTML',
                            'chat_id' => $userId,
                            'text' => $messageOut
                        ]);
                    } else {
                        $messageOut = 'Terdapat <strong>'.$countAll.' ayat ditemukan</strong>, silahkan pilih salah satu :';
                        $this->telegram->sendMessage([
                            'parse_mode'=>'HTML',
                            'chat_id' => $userId,
                            'text' => $messageOut
                        ]);
                        if ($countAll >= 100) {
                            $length = 0;
                            while ($countAll >= 100) {
                                $countAll -= 100;
                                $length++;
                            }
                            for ($i=0; $i <=$length; $i++) { 
                                $skip = $i * 100;
                                $find = DB::table('word_translations')
                                ->join('word_verses', 'word_translations.id_word_verse', '=', 'word_verses.id' )
                                ->join('verses', 'word_verses.id_verse', '=', 'verses.id')
                                ->join('chapters', 'verses.id_chapter', '=', 'chapters.id')
                                ->where('word_translations.text', 'LIKE', '%'.$chat[1].'%')
                                ->where('word_translations.id_language', '=', 13)
                                ->skip($skip)->take(100)
                                ->get(['word_verses.number as numberWord', 'verses.number as numberVerses', 'chapters.number_chapter', 'chapters.name']);

                                $messageQs = self::getQswAnswer($find);
                                $this->telegram->sendMessage([
                                    'parse_mode'=>'HTML',
                                    'chat_id' => $userId,
                                    'text' => $messageQs
                                ]);
                            }
                        } else {
                            $find = DB::table('word_translations')
                                ->join('word_verses', 'word_translations.id_word_verse', '=', 'word_verses.id' )
                                ->join('verses', 'word_verses.id_verse', '=', 'verses.id')
                                ->join('chapters', 'verses.id_chapter', '=', 'chapters.id')
                                ->where('word_translations.text', 'LIKE', '%'.$chat[1].'%')
                                ->where('word_translations.id_language', '=', 13)
                                ->get(['word_verses.number as numberWord', 'verses.number as numberVerses', 'chapters.number_chapter', 'chapters.name']);
                            $messageQs = self::getQswAnswer($find);
                            $this->telegram->sendMessage([
                                'parse_mode'=>'HTML',
                                'chat_id' => $userId,
                                'text' => $messageQs
                            ]);
                        }
                        
                    }
                } else {
                    $this->telegram->sendMessage([
                        'parse_mode'=>'HTML',
                        'chat_id' => $userId,
                        'text' => "Kata melebihi format"
                    ]);
                }
            } elseif ($chat[0] == '/qstrans') {
                if (sizeof($chat)<=1) {
                    $messageOut = self::Qcari_GUIDE;
                    $this->telegram->sendMessage([
                        'parse_mode' => 'HTML',
                        'chat_id' => $userId,
                        'text' => $messageOut
                    ]);
                } else {
                    $cariTrans = substr($input, 9);
                    $countTrns = DB::table('verse_translations')
                        ->join('verses', 'verse_translations.id_verse', '=', 'verses.id' )
                        ->join('chapters', 'verses.id_chapter', '=', 'chapters.id')
                        ->where('verse_translations.text', 'LIKE', '%'.$cariTrans.'%')
                        ->where('verse_translations.id_translation', '=', 33)
                        ->get()->count();

                    if ($countTrns == 0){
                        $messageOut = 'Terdapat <strong>'.$countTrns.' ayat ditemukan</strong>, silahkan coba dengan kata kunci lain.';
                        $this->telegram->sendMessage([
                            'parse_mode'=>'HTML',
                            'chat_id' => $userId,
                            'text' => $messageOut
                        ]);
                    } else {
                        $messageOut = 'Terdapat <strong>'.$countTrns.' ayat ditemukan</strong>, silahkan pilih salah satu :';
                        $this->telegram->sendMessage([
                            'parse_mode'=>'HTML',
                            'chat_id' => $userId,
                            'text' => $messageOut
                        ]);
                        if ($countTrns >= 100) {
                            $panjang = 0;
                            while ($countTrns >= 100) {
                                $countTrns -= 100;
                                $panjang++;                                
                            }
                            for ($i=0; $i <=$panjang; $i++) { 
                                $skip = $i * 100;
                                $translateVerse = DB::table('verse_translations')
                                    ->join('verses', 'verse_translations.id_verse', '=', 'verses.id' )
                                    ->join('chapters', 'verses.id_chapter', '=', 'chapters.id')
                                    ->where('verse_translations.text', 'LIKE', '%'.$cariTrans.'%')
                                    ->where('verse_translations.id_translation', '=', 33)
                                    ->skip($skip)->take(100)
                                    ->get(['verses.number', 'chapters.number_chapter', 'chapters.name']);

                                $messageQs = self::getQsvAnswer($translateVerse);
                                $this->telegram->sendMessage([
                                    'parse_mode'=>'HTML',
                                    'chat_id' => $userId,
                                    'text' => $messageQs
                                ]);
                            }

                        } else {
                            $translateVerse = DB::table('verse_translations')
                                ->join('verses', 'verse_translations.id_verse', '=', 'verses.id' )
                                ->join('chapters', 'verses.id_chapter', '=', 'chapters.id')
                                ->where('verse_translations.text', 'LIKE', '%'.$cariTrans.'%')
                                ->where('verse_translations.id_translation', '=', 33)
                                ->get(['verses.number', 'chapters.number_chapter', 'chapters.name']);

                                $messageQs = self::getQsvAnswer($translateVerse);
                                $this->telegram->sendMessage([
                                    'parse_mode'=>'HTML',
                                    'chat_id' => $userId,
                                    'text' => $messageQs
                                ]);
                            }
                        }
                    }
            } elseif ($chat[0] == '/lschap') {
                if (sizeof($chat) <= 1) {
                    $find = Chapters::all();

                    $messageIn = self::getChap($find);
                    $split = str_split($messageIn, 4096);
                    foreach ($split as $value) {
                        try {
                            $this->telegram->sendMessage([
                                'parse_mode' => 'HTML',
                                'chat_id' => $userId,
                                'text' => $value
                            ]);        
                        } catch (\Exception $e) {
                            $this->telegram->sendMessage([
                                'parse_mode' => 'HTML',
                                'chat_id' => $userId,
                                'text' => "Pesan Error"
                            ]);
                        }
                    }
                        
                } else {
                    $this->telegram->sendMessage([
                        'parse_mode' => 'HTML',
                        'chat_id' => $userId,
                        'text' => "Keyword Salah! Silahkan masukkan keyword lainnya"
                    ]);
                }
            } elseif ($chat2[0] == '/infochap') {
                if (sizeof($chat2) <= 1) {
                    $messageOut = "ini adalah menu info chapter...";
                    $this->telegram->sendMessage([
                        'parse_mode' => 'HTML',
                        'chat_id' => $userId,
                        'text' => $messageOut
                    ]);
                } elseif (sizeof($chat2) == 2) {
                    if ($chat2[1] <= 114 && $chat2[1] >= 1) {
                        $infoChap = Chapters::where('number_chapter', $chat2[1])->first();
                        $messageOut = self::infoChap($infoChap);
                        $this->telegram->sendMessage([
                            'parse_mode' => 'HTML',
                            'chat_id' => $userId,
                            'text' => $messageOut
                        ]);
                    } else {
                       $this->telegram->sendMessage([
                            'parse_mode' => 'HTML',
                            'chat_id' => $userId,
                            'text' => "Surah tidak di temukan."
                        ]); 
                    }
                }
            } else {
                $this->telegram->sendMessage([
                    'chat_id' => $userId,
                    'text' => "Menu not defined"
                ]);
            }
        } catch (\Exception $e) {
            $this->telegram->sendMessage([
                'chat_id' => $userId,
                'text' => error_log($e) 
            ]);
        }

        Storage::put('logs.txt', json_encode($request->all(), JSON_PRETTY_PRINT));
    }
    public static function arabicNumber($number){
        $western_arabic = array('0','1','2','3','4','5','6','7','8','9');
        $eastern_arabic = array('٠','١','٢','٣','٤','٥','٦','٧','٨','٩');

        return str_replace($western_arabic, $eastern_arabic, strval($number));
    }

    public function getQNAnswere($versesBetween, $chapters) {
        $message = '';
        foreach ($versesBetween as $item) {
            $message = $message
                .PHP_EOL.
                $item->text_uthmani .' ( '.self::arabicNumber($item->number).' )'
                .PHP_EOL.PHP_EOL.
                '{/qv_'.$item->id_chapter.'_'.$item->number.'}';

        }
        return '( '.self::arabicNumber($chapters->number_chapter).' )'.$chapters->arabic_name.
            PHP_EOL.
            $message.
            PHP_EOL;
    }

    public function getQwbwAnswere($word, $ayat) {
        $ayatMsg= $ayat->text_uthmani .' ( '.self::arabicNumber($ayat->number).' )';
        $chap = $ayat->id_chapter;
        $numAyat = $ayat->number;
        $message = '';
        foreach ($word as $item) {
            $message = $message
                .PHP_EOL.
                $item->text_uthmani.' ( '.self::arabicNumber($item->number).' )'
                .PHP_EOL.PHP_EOL.
                '{/qvwbw_'.$chap.'_'.$numAyat.'_'.$item->number.'}';

        }
        return $ayatMsg
            .PHP_EOL.
            $message
            .PHP_EOL;

    }

    public function getQvwbwAnswere($word, $ayat, $surah) {
        $chapter = '<strong>'.$surah->name . ' (QS'.$surah->chapters_number.':'.$ayat->number.')'.'</strong>';
        $surah = $ayat->id_chapter;
        $words = $word->text_uthmani.' ( '.self::arabicNumber($ayat->number).' )';
        return $chapter.
            PHP_EOL.
            PHP_EOL.
            $words.
            PHP_EOL.
            PHP_EOL.
            '<code>✏️Lihat Terjemahan Kata : </code>' .'{/qtwbw_'.$surah.'_'.$ayat->number.'_'.$word->number.'_13'.'}';

    }

    public function getQtwbwAnswere($word, $trans, $ayat) {
        $surah = $ayat->id_chapter;
        $words = $word->text_uthmani.' ( '.self::arabicNumber($ayat->number).' )';
        $word_translate = $trans->text;
        return 
            PHP_EOL.
            $words.
            PHP_EOL.
            PHP_EOL.
            '<strong>Artinya : </strong>'.$word_translate;

    }

    public function getQVAnswere(Verses $ayat, $chapter) {
        $ayatMsg= $ayat->text_uthmani .' ( '.self::arabicNumber($ayat->number).' )';

        return '<strong> ( '.self::arabicNumber($chapter->number_chapter).' )'.$chapter->arabic_name.'</strong>'
            .PHP_EOL.
            '<strong>'. $chapter->name. ' (QS '.$chapter->id.':'.$ayat->number.')'.'</strong>'
            .PHP_EOL.PHP_EOL.
            $ayatMsg
            .PHP_EOL.
            PHP_EOL.
            '<code>✏️Lihat Terjemahan Ayat : </code>' .'{/qtc_33_'.$chapter->id.'_'.$ayat->number.'}'.
            PHP_EOL
            .PHP_EOL.
            '<code>🆎Lihat Kata Per Kata : </code>' .'{/qwbw_'.$chapter->id.'_'.$ayat->number.'}'.
            PHP_EOL.
            PHP_EOL.
            '<code>📖Daftar Translator dan Cara Penggunaan : </code>' .'{/qtc}';

    }

    public function getTRC($translate) {
        $message = '';
        foreach ($translate as $item) {
            $message = $message
                .PHP_EOL.
                $item->text_uthmani .' ( '.self::arabicNumber($item->number).' )'
                .PHP_EOL.PHP_EOL.
                "<strong>Artinya : </strong>". strip_tags($item->text) . ' ( '.$item->number.' )'
                .PHP_EOL.PHP_EOL.
                "<strong>Translator : </strong>".$item->name
                .PHP_EOL.
                "<strong>Bahasa : </strong>".$item->language_name;
        }
        return $message;
    }

    public function getChap($surah) {
        $message = '';
        foreach ($surah as $item) {
            $number = $item->number_chapter;
            $message = $message
            .PHP_EOL.$number."). ".$item->name ."\t\t{/infochap_".$number."}";
        }
        return "<strong>Daftar Surah</strong>".PHP_EOL.$message;

    }

    public function infoChap($dataChap) {
        $name = $dataChap->name;
        $arabicName = $dataChap->arabic_name;
        $revelationOrder = $dataChap->revelation_order;
        $revelationPlace = $dataChap->revelation_place;
        $verseCount = $dataChap->verse_count;
        return PHP_EOL
        ."Name Surah\t\t\t\t\t\t: ". $name.PHP_EOL
        ."Name Arabic\t\t\t\t\t: ". $arabicName.PHP_EOL
        ."Urutan Turun\t\t\t\t: ". $revelationOrder.PHP_EOL
        ."Tempat Turun\t\t\t: ". $revelationPlace.PHP_EOL
        ."Jumlah Ayat\t\t\t\t\t\t\t: ". $verseCount;
    }

    public function getQswAnswer($keyword) {
        $number = 1;
        $message = '';
        foreach ($keyword as $value) {
            $message = $message
                .PHP_EOL.$number."). ".$value->name.'[/qvwbw_'.$value->number_chapter.'_'.$value->numberVerses.'_'.$value->numberWord.']'.
                PHP_EOL;
                $number++;
        }
        return $message;
    }

    public function getQsvAnswer($keyword) {
        $number = 1;
        $message = '';
        foreach ($keyword as $value) {
            $message = $message
                .PHP_EOL.$number."). ".$value->name.'[/qv_'.$value->number_chapter.'_'.$value->number.']'.
                PHP_EOL;
                $number++;

        }
        return $message;
    }

}
