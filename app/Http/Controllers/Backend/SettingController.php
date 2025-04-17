<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\SmtpSetting;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;


class SettingController extends Controller
{
    public function SmtpSetting()
    {
        $setting = SmtpSetting::find(1);
        return view('backend.setting.smpt_update', compact('setting'));
    }

    public function UpdateSmtpSetting(Request $request)
    {

        $stmp_id = $request->id;

        SmtpSetting::findOrFail($stmp_id)->update([

            'mailer' => $request->mailer,
            'host' => $request->host,
            'port' => $request->port,
            'username' => $request->username,
            'password' => $request->password,
            'encryption' => $request->encryption,
            'from_address' => $request->from_address,
        ]);


        $notification = array(
            'message' => 'Smtp Setting Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }

    public function SiteSetting()
    {
        $sitesetting = SiteSetting::first();
        return view('backend.setting.site_update', compact('sitesetting'));
    }

    public function UpdateSiteSetting(Request $request)
{
    $site_id = $request->id;
    $update_site = SiteSetting::findOrFail($site_id);

    // Common update data
    $updateData = [
        'support_phone' => $request->support_phone,
        'company_address' => $request->company_address,
        'email' => $request->email,
        'facebook' => $request->facebook,
        'twitter' => $request->twitter,
        'copyright' => $request->copyright,
    ];

    // Define paths
    $logoPath = public_path('upload/logo/');
    $bannerPath = public_path('upload/banner/');

    // Create directories if they do not exist
    if (!file_exists($logoPath)) {
        mkdir($logoPath, 0755, true);
    }

    if (!file_exists($bannerPath)) {
        mkdir($bannerPath, 0755, true);
    }

    // Handle logo upload
    if ($request->file('logo')) {
        // Unlink the old image if it exists
        $old_logo = public_path($update_site->logo);
        if (file_exists($old_logo) && !empty($update_site->logo)) {
            unlink($old_logo);
        }

        // Process and save the new logo
        $manager = new ImageManager(new Driver());
        $logo_name_gen = hexdec(uniqid()) . '.' . $request->file('logo')->getClientOriginalExtension();
        $logo_image = $manager->read($request->file('logo'));
        $logo_image = $logo_image->resize(1500, 386);
        $logo_image->toJpeg(80)->Save($logoPath . $logo_name_gen);
        $updateData['logo'] = 'upload/logo/' . $logo_name_gen;
    }

    // Handle banner photo upload
    if ($request->file('banner_photo')) {
        // Unlink the old image if it exists
        $old_banner = public_path($update_site->banner_photo);
        if (file_exists($old_banner) && !empty($update_site->banner_photo)) {
            unlink($old_banner);
        }

        // Process and save the new banner photo
        $manager = new ImageManager(new Driver());
        $banner_name_gen = hexdec(uniqid()) . '.' . $request->file('banner_photo')->getClientOriginalExtension();
        $banner_image = $manager->read($request->file('banner_photo'));
        $banner_image = $banner_image->resize(1920, 600); // Adjust size as needed
        $banner_image->toJpeg(80)->Save($bannerPath . $banner_name_gen);
        $updateData['banner_photo'] = 'upload/banner/' . $banner_name_gen;
    }

    // Perform the update
    $update_site->update($updateData);

    $notification = array(
        'message' => 'Site Setting Updated Successfully',
        'alert-type' => 'success'
    );

    return redirect()->back()->with($notification);
}

}
