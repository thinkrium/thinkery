<?php

class mediaRequirements {
  

   public function __construct() {
       
   }
   
   /*
    * returns the whitelist for all video extensions
    */
    public function video_whitelist() {
       
       return array(
           'webm', 'mkv', 'flv', 'vob', 'ogv', 'ogg', 'drc', 'gif', 'gifv', 
           'mng', 'avi', 'mov', 'qt', 'wmv', 'yuv', 'rm', 'rmvb', 'asf', 'mp4', 
           'm4p', 'm4v', 'mpg', 'mp2', 'mpeg', 'mpe', 'mpv', 'mpg', 'mpeg', 'm2v', 
           'm4v', 'svi', '3gp', '3g2', 'mxf', 'roq', 'nsv', 'flv', 'f4v', 'f4p',
           'f4a', 'f4b', 'mxf', 'avc', 'avchd', 'divx'
       );
   }

   /*
    * returns the whitelist for all audio extensions
    */
    public function audio_whitelist() {
       
       return array(
           '3gp','aa', 'aac', 'aax', 'act', 'aiff', 'amr', 'ape',  'au', 'awb',
           'dct', 'dss', 'dvf', 'flac', 'gsm', 'iklax', 'ivs', 'm4a', 'm4b', 'm4p',
           'mmf', 'mp3', 'mpc', 'msv', 'ogg', 'oga', 'opus', 'ra', 'rm', 'raw', 
           'sln', 'tta', 'vox', 'wav', 'wma', 'wv', 'webm'
        );
   }

   /*
    * returns the whitelist for all image file extensions
    */
    public function image_whitelist() {
       return array(
           'ani', 'anim', 'apng', 'art', 'bmp', 'bpg', 'bsave', 'cal', 'cin',
           'cpc', 'cpt', 'dds', 'dpx', 'ecw', 'exr', 'ff', 'fits', 'flic', "flif",
           'fpx', 'gif', 'hdri', 'hevc', 'icer', 'icns', 'ico', 'cur', 'ics',
           'ilbm', 'jbig', 'jbig2', 'jng', 'jpeg', 'jpeg-ls', 'xr', 'mng', 'miff',
           'nrrd', 'pam', 'pbm', 'pgm', 'ppm', 'pnm', 'pcx', 'pgf', 'pictor', 'png',
           'psd', 'psb', 'psp', 'qtvr', 'ras', 'rbe', 'tiff',
           'sgi', 'tga',  'wbmp', 'webp', 'xbm', 'xcf', 'xpm', 'xwd', 'ciff', 'dng',
           'ai', 'cdr', 'cgm', 'dxf', 'eva', 'emf', 'hvif', 'iges', 'pgml', 'svg', 'vml', 'wmf',
           'xar', 'cdf', 'djvu', 'eps', 'pdf', 'pict', 'ps', 'swf', 'xaml', 'exif', 'xmp'
        );
   }

   public function text_file_whitelist() {
        return array(
           'txt', 'doc'
        );
   }
   
}

