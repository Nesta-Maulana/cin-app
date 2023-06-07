<?php

use Carbon\Carbon;
use App\Models\Menu;
use App\Models\User;
use App\Models\Setting;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
      return strtoupper($initials);
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

/*** yes or no status ***/
if (! function_exists('overnight')) {
  function overnight($id) {
    if ($id) { 
      return '<span class="badge badge-center rounded-pill bg-label-primary" title="Next Day"><i class="ti ti-cloud"></i></span>';
    } else {
      return '<span class="badge badge-center rounded-pill bg-label-secondary"><i class="ti ti-sun-high"></i></span>';
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

/*** model list ***/
if (! function_exists('listModel')) {
  function listModel() {
    $modelList = [];
    $path = app_path() . "/Models";
    $results = scandir($path);

    foreach ($results as $result) {
      if ($result === '.' or $result === '..') continue;
      $filename = $result;

      if (is_dir($filename)) {
        $modelList = array_merge($modelList, getModels($filename));
      } else {
        $modelList[] = substr($filename,0,-4);
      }
    }

    return $modelList;
  }
}

/*** get menu header ***/
if (! function_exists('getMenuHeader')) {
  function getMenuHeader() {
    $menu = Menu::with('subMenu')->whereNull('main_menu')->where('is_header', true)->orderBy('sort', 'asc')->select('name', 'id')->get();
    return $menu;
  }
}

/*** get menu ***/
if (! function_exists('getMenu')) {
  function getMenu() {
    $menu = Menu::with('subMenu')->whereNotNull('icon')->orderBy('sort', 'asc')->get();
    return $menu;
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
    return asset("storage/{$image}");
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
    static $logo = null;
    if (is_null($logo)) {
      $setting = Setting::first();
      $logo = $setting ? imageAsset($setting->logo) : null;
    }
    return $logo;
  }
}

/*** app name ***/
if (! function_exists('appName')) {
  function appName() {
    static $appName = null;
    if (is_null($appName)) {
      $setting = Setting::first();
      $appName = $setting ? $setting->app_name . ' ' . $setting->app_version : null;
    }
    return $appName;
  }
}

/*** holder name ***/
if (! function_exists('holderName')) {
  function holderName() {
    static $holderName = null;
    if (is_null($holderName)) {
      $setting = Setting::first();
      $holderName = $setting ? $setting->name : null;
    }
    return $holderName;
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

/*** blood type ***/
if (! function_exists('bloodType')) {
  function bloodType() {
    return ['A', 'A+', 'A-', 'B', 'B+', 'B-', 'AB', 'AB+', 'AB-', 'O', 'O+', 'O-'];
  }
}

/*** class range ***/
if (! function_exists('classRange')) {
  function classRange($prefix, $first, $last) {
    $alphabet = range($first, $last);
    $result = array();
    foreach ($alphabet as $char) {
      $result[] = $prefix.' '.$char;
    }
    return $result;
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

/*** short school name ***/
if (! function_exists('shortName')) {
  function shortName($key) {
    $names = [
      'Markaz Al-Ma\'tuq' => 'Al-Ma\'tuq',
      'Markaz Al-Zamil' => 'Al-Zamil',
      'Markaz Tahfizh Al-Bassam' => 'Al-Bassam',
      'Markaz Tahfizh Al-\'Afaf' => 'Al-\'Afaf',
      'SD Muhammad Al-\'Unaizy' => 'Al-\'Unaizy',
      'TKIT Al-Ma\'tuq' => 'TKIT',
    ];
    return isset($names[$key]) ? $names[$key] : $key;
  }
}

/*** work unit ***/
if (! function_exists('workUnit')) {
  function workUnit($employee) {
    $unit = ($employee->employee_type == 2) ? $employee->school->name : $employee->employeeType->name;
    $division = $employee->division ? $employee->division->name : '';
    return compact('unit', 'division');
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

/*** shift kerja ***/
if (! function_exists('shift')) {
  function shift($employee) {
    if ($employee->is_reg_shift) {
      return $shift = $employee->regularShift->name;
    } else {
      return $shift = 'Custom';
    }
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