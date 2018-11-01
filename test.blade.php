<link href="https://cdnjs.cloudflare.com/ajax/libs/cropper/4.0.0/cropper.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<script src="https://code.jquery.com/jquery-3.2.1.js" ></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropper/4.0.0/cropper.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

{{-- image select and crop function in form --}}
<div class="form-group">
    <label for="input-image">Image:</label>
    <input type="file" id="input-image" name="image" onchange="loadFile(event)" accept="image/*" required/>
    <input type="hidden" id="image-x" name="image_x">
    <input type="hidden" id="image-y" name="image_y">
    <input type="hidden" id="image-width" name="image_width">
    <input type="hidden" id="image-height" name="image_height">

    <div class="image-pre">
        <img id="image" src="">
    </div>
</div>


{{-- Javascript function for crop --}}
<script>
var loadFile = function (event) {
    var output = document.getElementById('image');
    output.src = URL.createObjectURL(event.target.files[0]);
    var image = $('#image');
    var cropper = image.cropper({ 
        aspectRatio: 16 / 9,
        crop: function (e) {
            $('#image-x').val(e.detail.x);
            $('#image-y').val(e.detail.y);
            $('#image-width').val(e.detail.width);
            $('#image-height').val(e.detail.height); 
        }
    });
    $('.image-pre').css('height', '500');
};
</script>



@php
//  function in controller

   if ($request->file('image')) {

            // $old_img_o = base_path('../public_html/img/accommodation-slider-images/' . $data->img_o);
            // $old_img_t = base_path('../public_html/img/accommodation-slider-images/' . $data->img_t);

            // if (!is_dir($old_img_o) && file_exists($old_img_o)) {
            //     unlink($old_img_o);
            // }
            // if (!is_dir($old_img_t) && file_exists($old_img_t)) {
            //     unlink($old_img_t);
            // }

            $distPath = base_path('../public_html/img/images_list');
            $usrImage = $request->file('image')->getClientOriginalName();
            $x = $request->input('image_x');
            $y = $request->input('image_y');
            $width = $request->input('image_width');
            $height = $request->input('image_height');
            $imgurl = $request->file('image')->getRealPath();
            $uploadedData = $this->UploadImgWithCrop($distPath, $usrImage, $imgurl, $x, $y, $width, $height, $request->input('title'));
            $data->img_o = $uploadedData['o'];
            $data->img_t = $uploadedData['t'];

        }

 public function UploadImgWithCrop($distPath, $usrImage, $imgurl, $x, $y, $width, $height, $title)
    {
        $rand = rand(1, 1000);
        $fileName = pathinfo($usrImage, PATHINFO_FILENAME);
        $fileExt = pathinfo($usrImage, PATHINFO_EXTENSION);
        $fileNameToStore = 'crop_' . $title . $rand . $fileName . '.' . $fileExt;
        $fileNameToThumb = 'thumb_' . $title . $rand . $fileName . '.' . $fileExt;
        $img_url = $imgurl;
        $storeOn = $distPath . '/crop_' . $title . $rand . $usrImage;
        $storeOnT = $distPath . '/thumb_' . $title . $rand . $usrImage;
        $dbStoreName = $fileNameToStore;

        $width = round($width);
        $height = round($height);
        $x = round($x);
        $y = round($y);
        $img_1 = Image::make($img_url)->crop($width, $height, $x, $y)->save($storeOn);
        $img_2 = Image::make($storeOn)->resize(128, null, function ($constraint) {
            $constraint->aspectRatio();
        })->save($storeOnT);
        $ar = [];
        $ar['o'] = $fileNameToStore;
        $ar['t'] = $fileNameToThumb;
        return $ar;
    }
@endphp