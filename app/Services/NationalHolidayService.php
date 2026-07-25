<?php
namespace App\Services;
use App\Models\Holiday; use Illuminate\Support\Facades\Http; use RuntimeException;
class NationalHolidayService { public function sync(int $year):int { $response=Http::timeout(10)->get("https://date.nager.at/api/v4/Holidays/ID/{$year}"); if(!$response->successful()) throw new RuntimeException('Layanan kalender nasional tidak tersedia.'); $count=0; foreach($response->json() as $item){if(!($item['nationalHoliday']??true))continue; Holiday::updateOrCreate(['holiday_date'=>$item['date']],['name'=>'Nasional: '.($item['name']??'Hari Libur Nasional'),'is_active'=>true]);$count++;} return $count; } }
