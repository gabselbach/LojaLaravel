<?php
/**
 * Created by PhpStorm.
 * User: gabriella
 * Date: 09/12/20
 * Time: 17:08
 */

namespace  App\Traits;

use Illuminate\Http\Request;

trait UploadTrait
{
    private  function imageUpload($images,$imageColumn= null)
    {


        $uploadImages = [];
        if (is_array($images)) {
            foreach ($images as $image) {
                $uploadImages[] = [$imageColumn => $image->store('products', 'public')];
            }
        }else {
                $uploadImages = $images->store('logo', 'public');
            }
        return $uploadImages;
    }
}