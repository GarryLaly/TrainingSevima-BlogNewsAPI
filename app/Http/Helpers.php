<?php

use Illuminate\Support\Str;
use Carbon\Carbon;

function compressImage($image, $basepath, $size = [1920, 480]) {
    $unique = Str::random(10);
    $filename = md5(time() . '-' . $unique) . '.jpeg';
    $path = public_path("$basepath/$filename");
    $thumbPath = public_path("$basepath/thumb/$filename");

    if (!File::isDirectory(public_path("$basepath"))) {
        File::makeDirectory(public_path("$basepath"), 0755, true);
    }

    if (!File::isDirectory(public_path("$basepath/thumb"))) {
        File::makeDirectory(public_path("$basepath/thumb"), 0755, true);
    }

    $setConstraint = function ($constraint) {
        $constraint->aspectRatio();
        $constraint->upsize();
    };

    Image::make($image)
        ->resize($size[0], null, $setConstraint)
        ->save($path, 100, 'jpeg');

    Image::make($image)
        ->resize($size[1], null, $setConstraint)
        ->save($thumbPath, 100, 'jpeg');

    return $filename;
}

function deleteImage($path, $filename) {
    $realPath = public_path("$path/$filename");
    $thumbPath = public_path("$path/thumb/$filename");

    if ($filename && file_exists($realPath)) {
        unlink($realPath);
    }

    if ($filename && file_exists($thumbPath)) {
        unlink($thumbPath);
    }
}

function trimText($text) {
    return $text === NULL ? NULL : trim($text);
}

function generateCode($prefix, $model) {
    $date = now()->isoFormat('YYMMDD');
    $totalTodayRecords = $model::whereDate('created_at', Carbon::today())->count();
    $increment = $totalTodayRecords + 1;

    return $prefix . $date . $increment;
}

function isPermitted($routeName) {
    $user = auth()->user();
    $menuPrivileges = $user->menuPrivileges;

    $isPermitted = !!$menuPrivileges->first(function($privilege) use ($routeName) {
        $isValidRoute = $privilege->menuAdmin->link === $routeName;
        $isAccessible = $privilege->can_access === 1;

        return $isValidRoute && $isAccessible;
    });

    return $isPermitted;
}

function isPermittedOneOf($routeNames) {
    return collect($routeNames)->map('isPermitted')->contains(TRUE);
}
