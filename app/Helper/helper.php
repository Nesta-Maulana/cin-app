<?php

use Carbon\Carbon;
use App\Models\Menu;
use App\Models\User;
use App\Models\Setting;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/*
Admin Helper
*/

/*** initial user ***/
if (!function_exists('userInitial')) {
    function userInitial($id)
    {
        $user = User::findOrFail($id);
        $words = explode(" ", $user->name);
        $initials = null;
        foreach ($words as $w) {
            $initials .= $w[0];
        }
        return substr(strtoupper($initials), 0, 2);
    }
}

/*** my initial ***/
if (!function_exists('myInitial')) {
    function myInitial()
    {
        $auth = auth()->user();
        if ($auth) {
            $words = explode(" ", $auth->name);
            $initials = null;
            foreach ($words as $w) {
                $initials .= $w[0];
            }
            return substr(strtoupper($initials), 0, 2);
        }
    }
}

/*** active status ***/
if (!function_exists('isActive')) {
    function isActive($id, $status)
    {
        $data = '<div class="demo-inline-spacing" style="justify-content:space-around">';
        if ($status) {
            $data .= '<div class="form-check form-check-success form-switch">';
            $data .= '<input type="id" hidden name="id" value="' . $id . '">';
            $data .= '<input type="checkbox" name="status" class="form-check-input" checked="true" wire:click="updateStatus(\'' . $id . '\',$event.target.checked)">';
            $data .= '<label class="" for="status">Active</label>';
            $data .= '</div>';
        } else {
            $data .= '<div class="form-check form-check-danger form-switch">';
            $data .= '<input type="id" hidden name="id" value="' . $id . '">';
            $data .= '<input type="checkbox" name="status" class="form-check-input" wire:click="updateStatus(\'' . $id . '\',$event.target.checked)">';
            $data .= '<label class="" for="status">Inactive</label>';
            $data .= '</div>';
        }
        $data .= '</div>';
        return $data;
    }
}

/*** yes or no status ***/
if (!function_exists('yesOrNo')) {
    function yesOrNo($id)
    {
        if ($id) {
            return '<span class="badge badge-center rounded-pill bg-primary"><i class="ti ti-check"></i></span>';
        } else {
            return '<span class="badge badge-center rounded-pill bg-label-secondary"><i class="ti ti-x"></i></span>';
        }
    }
}

/*** sweetalert notif ***/
if (!function_exists('alertNotif')) {
    function alertNotif($type, $message = null)
    {
        return match ($type) {
            'save' => toast('Data Saved! / 数据已保存！', 'success'),
            'update' => toast('Data Updated! / 数据已更新！', 'success'),
            'delete' => toast('Data Deleted! / 数据已删除！', 'success'),
            'success' => toast(!is_null($message) ? $message : 'Process Successful / 处理成功', 'success'),
            'error' => toast(!is_null($message) ? $message : 'Oops! There was an error / 哎呀！发生错误', type: 'error'),
        };
    }
}

/*** sweetalert notif livewire message ***/
if (!function_exists('alertMsg')) {
    function alertMsg($message)
    {
        if ($message == 'save') {
            return 'Data Tersimpan!';
        } elseif ($message == 'update') {
            return 'Data Diperbarui!';
        } elseif ($message == 'delete') {
            return 'Data Dihapus!';
        }
    }
}

/*** role color ***/
if (!function_exists('roleColor')) {
    function roleColor($role)
    {
        if ($role == 'Super Admin') {
            return 'primary';
        } elseif ($role == 'Administrator') {
            return 'primary';
        } elseif ($role == 'Lead Team') {
            return 'info';
        } elseif ($role == 'Operator') {
            return 'success';
        } elseif ($role == 'Guru') {
            return 'danger';
        } else {
            return 'warning';
        }
    }
}

/*** get menu ***/
if (!function_exists('getMenu')) {
    function getMenu()
    {
        return cache()->rememberForever('menu', function () {
            $menu = Menu::with([
                'subMenu' => function ($query) {
                    $query->with('permission');
                }
            ])
                ->select('id', 'name', 'url', 'permission_id', 'icon', 'main_menu', 'sort')
                ->justParent()
                ->sorted()
                ->get();
            return $menu;
        });
    }
}

/*** check akses menu ***/
if (!function_exists('canAccessMenu')) {
    function canAccessMenu($menu)
    {
        if ($menu->subMenu->count() > 0) {
            foreach ($menu->subMenu as $subMenu) {
                if (!$subMenu->permission || Auth::user()->can($subMenu->permission->name)) {
                    return true;
                }
            }
            return false;
        } else {
            if (!$menu->permission || Auth::user()->can($menu->permission->name)) {
                return true;
            }
        }
    }
}

/*** image asset ***/
if (!function_exists('imageAsset')) {
    function imageAsset($image)
    {
        if (!$image) {
            return asset("Thumbnail.jpg");
        } else {
            return asset("storage/{$image}");
        }
    }
}

/*** image preview ***/
if (!function_exists('imagePreview')) {
    function imagePreview($image)
    {
        if (!$image) {
            return asset("Thumbnail.jpg");
        } else {
            return asset("storage/{$image}");
        }
    }
}

/*** store image ***/
if (!function_exists('storeImage')) {
    function storeImage($request, $name, $folder)
    {
        $image = $request->hasFile($name) ? $request->file($name)->store($folder) : '';
        return $image;
    }
}

/*** update image ***/
if (!function_exists('updateImage')) {
    function updateImage($request, $name, $folder, $old_image = null)
    {
        if ($request->hasFile($name)) {
            if ($old_image && Storage::exists($old_image)) {
                Storage::delete($old_image);
            }

            $new_image = $request->file($name)->store($folder);
            return $new_image;
        }

        return $old_image;
    }
}

/*** primary logo ***/
if (!function_exists('primaryLogo')) {
    function primaryLogo()
    {
        return cache()->rememberForever('primary_logo', function () {
            static $logo = null;
            if (is_null($logo)) {
                $setting = Setting::find('2');
                $setting = json_decode($setting->data);
                $logo = $setting && $setting->logo_1 ? imageAsset($setting->logo_1) : asset('default.png');
            }
            return $logo;
        });
    }
}
/*** primary logo ***/
if (!function_exists('secondaryLogo')) {
    function secondaryLogo()
    {
        return cache()->rememberForever('secondary_logo', function () {
            static $logo = null;
            if (is_null($logo)) {
                $setting = Setting::find('2');
                $setting = json_decode($setting->data);
                $logo = $setting && $setting->logo_2 ? imageAsset($setting->logo_2) : asset('default.png');
            }
            return $logo;
        });
    }
}

/*** site logo ***/
if (!function_exists('siteLogo')) {
    function siteLogo()
    {
        return cache()->rememberForever('site_logo', function () {
            static $logo = null;
            if (is_null($logo)) {
                $setting = Setting::find('1');
                $setting = json_decode($setting->data);
                $logo = $setting && $setting->logo ? imageAsset($setting->logo) : asset('default.png');
                if (!file_exists($logo)) {
                    $logo = asset('default.png');
                }
            }
            return $logo;
        });
    }
}

/*** app name ***/
if (!function_exists('appName')) {
    function appName()
    {
        return cache()->rememberForever('app_name', function () {
            static $appName = null;
            if (is_null($appName)) {
                $setting = Setting::find('1');
                $setting = json_decode($setting->data);
                $appName = $setting ? $setting->app_name : 'Anveshana';
            }
            return $appName;
        });
    }
}
if (!function_exists('appVersion')) {
    function appVersion()
    {
        return cache()->rememberForever('app_version', function () {
            static $appVersion = null;
            if (is_null($appVersion)) {
                $setting = Setting::find('1');
                $setting = json_decode($setting->data);
                $appVersion = $setting ? $setting->app_version : null;
            }
            return $appVersion;
        });
    }
}

/*** holder name ***/
if (!function_exists('holderName')) {
    function holderName()
    {
        return cache()->rememberForever('holder_name', function () {
            static $holderName = null;
            if (is_null($holderName)) {
                $setting = Setting::find('1');
                $setting = json_decode($setting->data);
                $holderName = $setting ? $setting->name : null;
            }
            return $holderName;
        });
    }
}


/*** Header Banner Text ***/
if (!function_exists('headerBannerText')) {
    function headerBannerText()
    {
        return cache()->rememberForever('header_banner_text', function () {
            static $headerBannerText = null;
            if (is_null($headerBannerText)) {
                $setting = Setting::find('2');
                $setting = json_decode($setting->data);
                $headerBannerText = $setting ? $setting?->header_banner_text : null;
            }
            return $headerBannerText;
        });
    }
}
/*** Header Banner Text Highlight ***/
if (!function_exists('headerBannerTextHighlight')) {
    function headerBannerTextHighlight()
    {
        return cache()->rememberForever('header_banner_text_highlight', function () {
            static $headerBannerTextHighlight = null;
            if (is_null($headerBannerTextHighlight)) {
                $setting = Setting::find('2');
                $setting = json_decode($setting->data);
                $headerBannerTextHighlight = $setting ? $setting?->header_banner_text_highlight : null;
            }
            return $headerBannerTextHighlight;
        });
    }
}
/*** Title Instansi Terkait Highlight ***/
if (!function_exists('titleInstansiTerkait')) {
    function titleInstansiTerkait()
    {
        return cache()->rememberForever('title_instansi_terkait_section', function () {
            static $titleInstansiTerkait = null;
            if (is_null($titleInstansiTerkait)) {
                $setting = Setting::find('2');
                $setting = json_decode($setting->data);
                $titleInstansiTerkait = $setting ? $setting?->title_instansi_terkait_section : null;
            }
            return $titleInstansiTerkait;
        });
    }
}
/*** Body Instansi Terkait Highlight ***/
if (!function_exists('bodyInstansiTerkait')) {
    function bodyInstansiTerkait()
    {
        return cache()->rememberForever('body_instansi_terkait_section', function () {
            static $bodyInstansiTerkait = null;
            if (is_null($bodyInstansiTerkait)) {
                $setting = Setting::find('2');
                $setting = json_decode($setting->data);
                $bodyInstansiTerkait = $setting ? $setting?->body_instansi_terkait_section : null;
            }
            return $bodyInstansiTerkait;
        });
    }
}
/*** Body Instansi Terkait Highlight ***/
if (!function_exists('address')) {
    function address()
    {
        return cache()->rememberForever('address', function () {
            static $address = null;
            if (is_null($address)) {
                $setting = Setting::find('1');
                $setting = json_decode($setting->data);
                $address = $setting ? $setting?->address : null;
            }
            return $address;
        });
    }
}

/*** list year ***/
if (!function_exists('getYearRange')) {
    function getYearRange()
    {
        $years = [];
        for ($year = 2020; $year <= 2030; $year++) {
            $years[] = $year;
        }

        return $years;
    }
}

/*** this year ***/
if (!function_exists('thisYear')) {
    function thisYear()
    {
        return Carbon::now()->format('Y');
    }
}

/*** alphabet list ***/
if (!function_exists('alphabetList')) {
    function alphabetList()
    {
        return range('A', 'Z');
    }
}

/*** date format ***/
if (!function_exists('dateDMY')) {
    function dateDMY($date)
    {
        return date('d-m-Y', strtotime($date));
    }
}

/*** number format ***/
if (!function_exists('numberFormat')) {
    function numberFormat($value)
    {
        return number_format($value, 0, ',', '.');
    }
}

/*** nama hari ***/
if (!function_exists('daysName')) {
    function daysName()
    {
        return ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    }
}

/*** format jam menit ***/
if (!function_exists('hourMin')) {
    function hourMin($time)
    {
        return $time ? substr($time, 0, 5) : '';
    }
}

/*** working time ***/
if (!function_exists('since')) {
    function since($start, $end)
    {
        $start = Carbon::parse($start);
        $end = Carbon::parse($end);
        $diff = $end->diff($start);
        $time = ($diff->y > 0) ? $diff->y . " tahun " : '';
        $time .= ($diff->m > 0) ? $diff->m . " bulan " : '';
        $time .= ($diff->d > 0) ? $diff->d . " hari" : '';
        return trim($time);
    }
}

/*** sekarang ***/
if (!function_exists('now')) {
    function now()
    {
        return Carbon::now();
    }
}

/*** periode bulanan ***/
if (!function_exists('monthlyPeriod')) {
    function monthlyPeriod($month)
    {
        $startDate = Carbon::parse($month)->startOfMonth()->subMonths(1)->addDays(25);
        $endDate = Carbon::parse($month)->startOfMonth()->addDays(24);
        return CarbonPeriod::create($startDate, $endDate);
    }
}

if (!function_exists('formatDate')) {
    function formatDate($dateString)
    {
        $date = Carbon::parse($dateString);
        $formattedDate = $date->locale('id')->isoFormat('DD MMMM YYYY');
        return $formattedDate;

    }
}
if (!function_exists('formatDateTime')) {
    function formatDateTime($dateString)
    {
        $date = Carbon::parse($dateString);
        $formattedDate = $date->locale('id')->isoFormat('DD MMMM YYYY HH:mm');
        return $formattedDate;

    }
}
if (!function_exists('formatDateTimeJKT')) {
    function formatDateTimeJKT($dateString)
    {
        $date = Carbon::parse($dateString);
        // Convert to a specific timezone (e.g., Asia/Jakarta)
        $date->setTimezone('Asia/Jakarta');
        // Format the date to the desired format
        return $date->format('Y-m-d H:i:s');
    }
}


function getModels()
{
    $path = app_path('Models');
    $files = scandir($path);

    $models = [];
    foreach ($files as $file) {
        if (preg_match('/\.php$/', $file)) {
            $models[] = 'App\\Models\\' . str_replace('.php', '', $file);
        }
    }

    return $models;
}

function getCurrency()
{
    $currenciesResponse = Http::get('https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies.json');
    $currencies = $currenciesResponse->json();
    return $currencies;
}

function getQuantity($detail)
{
    $quantity = $detail->quantity;
    if($detail->itemPriceHistory->itemUom->unitOfMeasurement->id !== $detail->itemPriceHistory->itemUom->item->unitOfMeasurement->id)
    {
        $quantity = $detail->quantity * $detail->itemPriceHistory->itemUom->conversion;
    }
    $quantities = $quantity.' '.$detail->itemPriceHistory->itemUom->item->unitOfMeasurement->name.' | ';
    foreach ($detail->itemPriceHistory->itemUom->item->itemUoms->sortBy('conversion') as $key => $uom)
    {
        if ($uom->unitOfMeasurement->id !== $detail->itemPriceHistory->itemUom->item->unitOfMeasurement->id) {
            $quantities .= number_format($quantity / $uom->conversion, 2, ',', '.') . ' ' . $uom->unitOfMeasurement->name.' | ';
        }
    }
    return rtrim($quantities, ' | ');
}
function getQuantityByItem($item, $quantity,$defaultUom)
{
    $quantities = $quantity.' '.$defaultUom.' | ';
    foreach ($item->itemUoms->sortBy('conversion') as $key => $uom)
    {
        if ($uom->unitOfMeasurement->id !== $defaultUom) {
            $quantities .= number_format($quantity / $uom->conversion, 2, ',', '.') . ' ' . $uom->unitOfMeasurement->name.' | ';
        }
    }
    return rtrim($quantities, ' | ');
}

