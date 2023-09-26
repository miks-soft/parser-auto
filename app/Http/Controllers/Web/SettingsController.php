<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\SettingsUpdateRequest;
use App\Models\SiteSettings;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function update(SettingsUpdateRequest $request)
    {
        $list = $request->input('list');
        var_dump($list);
        foreach ($list as $item)
        {
            SiteSettings::query()
                ->where('slug', $item['key'])
                ->update([
                    'value'=>$item['value']
                ]);
        }
        return redirect(route('home'))
            ->with('success', true);
    }
}
