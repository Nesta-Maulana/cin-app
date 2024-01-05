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
if (! function_exists('userInitial')) {
  function userInitial($id) {
    $user = User::findOrFail($id);
    $words = explode(" ", $user->name );
    $initials = null;
    foreach ($words as $w) {
      $initials .= $w[0];
    }
    return substr(strtoupper($initials), 0, 2);
  }
}

/*** my initial ***/
if (! function_exists('myInitial')) {
  function myInitial() {
    $auth = auth()->user();
    if($auth) {
      $words = explode(" ", $auth->name );
      $initials = null;
      foreach ($words as $w) {
        $initials .= $w[0];
      }
      return substr(strtoupper($initials), 0, 2);
    }
  }
}

/*** active status ***/
if (! function_exists('isActive')) {
  function isActive($id) {
    if ($id) {
      return '<span class="badge bg-label-success" text-capitalized="">Active</span>';
    } else {
      return '<span class="badge bg-label-danger" text-capitalized="">Inactive</span>';
    }
  }
}

/*** yes or no status ***/
if (! function_exists('yesOrNo')) {
  function yesOrNo($id) {
    if ($id) {
      return '<span class="badge badge-center rounded-pill bg-primary"><i class="ti ti-check"></i></span>';
    } else {
      return '<span class="badge badge-center rounded-pill bg-label-secondary"><i class="ti ti-x"></i></span>';
    }
  }
}

/*** sweetalert notif ***/
if (! function_exists('alertNotif')) {
  function alertNotif($message) {
    if ($message == 'save') {
      return toast('Data Tersimpan!','success');
    } elseif ($message == 'update') {
      return toast('Data Diperbarui!','success');
    } elseif ($message == 'delete') {
      return toast('Data Dihapus!','success');
    } elseif ($message == 'error') {
      return toast('Oops! Ada kesalahan','error');
    }
  }
}

/*** sweetalert notif livewire message ***/
if (! function_exists('alertMsg')) {
  function alertMsg($message) {
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
if (! function_exists('roleColor')) {
  function roleColor($role) {
    if ($role == 'Super Admin') {
      return 'primary';
    } elseif ($role == 'Admin') {
      return 'primary';
    } elseif ($role == 'Director') {
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
if (! function_exists('getMenu')) {
  function getMenu() {
    return cache()->rememberForever('menu', function () {
      $menu = Menu::with('subMenu')
        ->select('id', 'name', 'url', 'permission_id', 'icon', 'main_menu', 'sort')
        ->withIcon()
        ->sorted()
        ->get();

      return $menu;
    });
  }
}

/*** check akses menu ***/
if (!function_exists('canAccessMenu')) {
  function canAccessMenu($menu) {
    if ($menu->hasSubMenu()) {
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
if (! function_exists('imageAsset')) {
  function imageAsset($image) {
    if (!$image) {
        return asset("Thumbnail.jpg");
    } else {
        return asset("storage/{$image}");
    }
  }
}

/*** image preview ***/
if (! function_exists('imagePreview')) {
  function imagePreview($image) {
    if(!$image) {
      return asset("Thumbnail.jpg");
    } else {
      return asset("storage/{$image}");
    }
  }
}

/*** store image ***/
if (! function_exists('storeImage')) {
  function storeImage($request, $name, $folder)
  {
    $image = $request->hasFile($name) ? $request->file($name)->store($folder) : '';
    return $image;
  }
}

/*** update image ***/
if (! function_exists('updateImage')) {
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

/*** site logo ***/
if (! function_exists('siteLogo')) {
  function siteLogo() {
    return cache()->rememberForever('site_logo', function () {
      static $logo = null;
      if (is_null($logo)) {
        $setting = Setting::select('logo')->first();
        $logo = $setting ? imageAsset($setting->logo) : null;
      }
      return $logo;
    });
  }
}

/*** app name ***/
if (! function_exists('appName')) {
  function appName() {
    return cache()->rememberForever('app_name', function () {
      static $appName = null;
      if (is_null($appName)) {
        $setting = Setting::select('app_name', 'app_version')->first();
        $appName = $setting ? $setting->app_name . ' ' . $setting->app_version : null;
      }
      return $appName;
    });
  }
}

/*** holder name ***/
if (! function_exists('holderName')) {
  function holderName() {
    return cache()->rememberForever('holder_name', function () {
      static $holderName = null;
      if (is_null($holderName)) {
        $setting = Setting::select('name')->first();
        $holderName = $setting ? $setting->name : null;
      }
      return $holderName;
    });
  }
}

/*** list year ***/
if (! function_exists('getYearRange')) {
  function getYearRange() {
    $years = [];
    for ($year = 2020; $year <= 2030; $year++) {
      $years[] = $year;
    }

    return $years;
  }
}

/*** this year ***/
if (! function_exists('thisYear')) {
  function thisYear() {
    return Carbon::now()->format('Y');
  }
}

/*** alphabet list ***/
if (!function_exists('alphabetList')) {
    function alphabetList() {
      return range('A', 'Z');
    }
}

/*** date format ***/
if (!function_exists('dateDMY')) {
    function dateDMY($date) {
      return date('d-m-Y', strtotime($date));
    }
}

/*** number format ***/
if (! function_exists('numberFormat')) {
  function numberFormat($value) {
    return number_format($value, 0, ',', '.');
  }
}

/*** nama hari ***/
if (! function_exists('daysName')) {
  function daysName() {
    return ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
  }
}

/*** format jam menit ***/
if (! function_exists('hourMin')) {
  function hourMin($time) {
    return $time ? substr($time, 0, 5) : '';
  }
}

/*** working time ***/
if (! function_exists('since')) {
  function since($start, $end) {
    $start = Carbon::parse($start);
    $end = Carbon::parse($end);
    $diff = $end->diff($start);
    $time = ($diff->y > 0) ? $diff->y." tahun " : '';
    $time .= ($diff->m > 0) ? $diff->m." bulan " : '';
    $time .= ($diff->d > 0) ? $diff->d." hari" : '';
    return trim($time);
  }
}

/*** sekarang ***/
if (! function_exists('now')) {
  function now() {
    return Carbon::now();
  }
}

/*** periode bulanan ***/
if (! function_exists('monthlyPeriod')) {
  function monthlyPeriod($month) {
    $startDate = Carbon::parse($month)->startOfMonth()->subMonths(1)->addDays(25);
    $endDate = Carbon::parse($month)->startOfMonth()->addDays(24);
    return CarbonPeriod::create($startDate, $endDate);
  }
}
