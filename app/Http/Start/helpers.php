<?php

namespace App\Http\Start;

use View;
use Session;
use App\Models\Metas;
use Image;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;
use App\Models\Currency;

class Helpers
{
	// Set Flash Message function
	public function flash_message($class, $message)
	{
		Session::flash('alert-class', 'alert-'.$class);
	    Session::flash('message', $message);
	}

	public function compress_image($source_url, $destination_url, $quality, $width = 225, $height = 225) 
	{
        $info = getimagesize($source_url);

            if ($info['mime'] == 'image/jpeg')
                    $image = imagecreatefromjpeg($source_url);

            elseif ($info['mime'] == 'image/gif')
                    $image = imagecreatefromgif($source_url);

        elseif ($info['mime'] == 'image/png')
                    $image = imagecreatefrompng($source_url);

            imagejpeg($image, $destination_url, $quality);

        $this->crop_image($source_url, $width, $height);

        return $destination_url;
    }

    // Dynamic Function for Showing Meta Details
    public static function meta($url, $field)
    {
        $metas = Metas::where('url', $url);
    
        if($metas->count())
            return $metas->first()->$field;
        else if($field == 'title')
            return 'Page Not Found';
        else
            return '';
    }

    public function crop_image($source_url='', $crop_width=225, $crop_height=225, $destination_url = '')
    {
    	$image = Image::make($source_url); 
        $image_width = $image->width();
        $image_height = $image->height();

        if($image_width < $crop_width && $crop_width < $crop_height){
            $image = $image->fit($crop_width, $image_height);
        }if($image_height < $crop_height  && $crop_width > $crop_height){
            $image = $image->fit($crop_width, $crop_height);
        }

        // if($image_width > $image_height){
        // 	$primary_crop_width = $image_height; 
        // 	$primary_crop_height = $image_height;

        // 	$primary_x = round(($image_width - $image_height)/2);
        // 	$primary_y = 0; 

        // }if($image_width <= $image_height){
        // 	$primary_crop_width = $image_width; 
        // 	$primary_crop_height = $image_width; 

        // 	$primary_x = 0;
        // 	$primary_y = $image_width < $image_height ? round(($image_height - $image_width)/2) : 0; 
        		
        // }

        // $primary_cropped_image = $image->crop($primary_crop_width, $primary_crop_height, $primary_x, $primary_y); 
  		$primary_cropped_image = $image;

        $croped_image = $primary_cropped_image->fit($crop_width, $crop_height);

		if($destination_url == ''){
			$source_url_details = pathinfo($source_url); 
			$destination_url = @$source_url_details['dirname'].'/'.@$source_url_details['filename'].'_'.$crop_width.'x'.$crop_height.'.'.@$source_url_details['extension']; 
		}
		$croped_image->save($destination_url); 
		return $destination_url; 
    }

    public static function buildExcelFile($filename, $data, $width = array())
    {
        /** @var \Maatwebsite\Excel\Excel $excel */
        $excel = app('excel');

        $excel->getDefaultStyle()
        ->getAlignment()
        ->setHorizontal('left');
        foreach ($data as $key => $array) {
            foreach ($array as $k => $v) {
                if(!$v){
                    $data[$key][$k] = '--';
                }
            }
        }

        // dd($filename, $data, $width);
        return $excel->create($filename, function (LaravelExcelWriter $excel) use($data, $width){
            $excel->sheet('exported-data', function (LaravelExcelWorksheet $sheet) use($data, $width) {
                $sheet->fromArray($data)->setWidth($width);
                $sheet->setAllBorders('thin');
            });
        });
    }

    /** 
     * Get the formatted Co-ordinated data
     *
     * @param string $coordinate_data string contains polygon co-ordinates
     * @return Array $formatted_coordinates
     */ 
    public function getFormattedCoordinates($coordinate_data)
    {
        $formatted_coordinates = [];
        if(!$coordinate_data) {
            return $formatted_coordinates;
        }
        $coordinate_data = ltrim(rtrim($coordinate_data,')'),'(');
        $coordinate_data = explode(',', $coordinate_data);
        $i = 0;
        foreach ($coordinate_data as $coords) {
            $coord = explode(' ', trim($coords));
            $return_value[$i]['lat'] = (float) $coord[0];
            $return_value[$i]['lng'] = (float) $coord[1];
            $i++;
        }
        $formatted_coordinates[0] = $return_value;
        return $formatted_coordinates;
    }

    /** 
     * create time range 
     *  
     * @param mixed $start start time 
     * @param mixed $end   end time
     * @param string $interval time intervals, 1 hour, 1 mins, 1 secs, etc.
     * @param string $format time format, e.g., 12 or 24
     */ 
    function create_time_range($start, $end, $interval = '30 mins', $format = '12')
    {
        $startTime = strtotime($start); 
        $endTime   = strtotime($end);
        $returnTimeFormat = ($format == '12')?'h:i A':'H:i A';

        $current   = time();
        $addTime   = strtotime('+'.$interval, $current); 
        $diff      = $addTime - $current;

        $times = array(); 
        while ($startTime < $endTime) { 
            $times[date('H:i:s',$startTime)] = date($returnTimeFormat, $startTime); 
            $startTime += $diff; 
        }

        $times[date('H:i:s',$startTime)] = date($returnTimeFormat, $startTime); 
        return $times; 
    }
    
	/** 
     * create time range 
     *
     * @return Array $day_options
     */ 
    function get_day_options()
    {
        $day_names = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
        $day_options = [];
        foreach ($day_names as $day_name) {
            $day_options[date('N',strtotime($day_name))] = $day_name;
        }

        return $day_options;
    }
    
    // get stripe supported currency
    public function getStripeCurrency($country = '')
    {
        $currency = [];
        $currency['AT'] = ['EUR','DKK','GBP','NOK','SEK','USD','CHF'];
        $currency['AU'] = ['AUD'];
        $currency['BE'] = ['EUR','DKK','GBP','NOK','SEK','USD','CHF'];
        $currency['CA'] = ['CAD','USD'];
        $currency['GB'] = ['GBP','EUR','DKK','NOK','SEK','USD','CHF'];
        $currency['HK'] = ['HKD'];
        $currency['JP'] = ['JPY'];
        $currency['NZ'] = ['NZD'];
        $currency['SG'] = ['SGD'];
        $currency['US'] = ['USD'];
        $currency['CH'] = ['CHF','EUR','DKK','GBP','NOK','SEK','USD'];
        $currency['DE'] = ['EUR','DKK','GBP','NOK','SEK','USD','CHF'];
        $currency['DK'] = ['DKK','EUR','GBP','NOK','SEK','USD','CHF'];
        $currency['ES'] = ['EUR','DKK','GBP','NOK','SEK','USD','CHF'];
        $currency['FI'] = ['EUR','DKK','GBP','NOK','SEK','USD','CHF'];
        $currency['FR'] = ['EUR','DKK','GBP','NOK','SEK','USD','CHF'];
        $currency['IE'] = ['EUR','DKK','GBP','NOK','SEK','USD','CHF'];
        $currency['IT'] = ['EUR','DKK','GBP','NOK','SEK','USD','CHF'];
        $currency['LU'] = ['EUR','DKK','GBP','NOK','SEK','USD','CHF'];
        $currency['NL'] = ['EUR','DKK','GBP','NOK','SEK','USD','CHF'];
        $currency['NO'] = ['NOK','EUR','DKK','GBP','SEK','USD','CHF'];
        $currency['PT'] = ['EUR','DKK','GBP','NOK','SEK','USD','CHF'];
        $currency['SE'] = ['SEK','EUR','DKK','GBP','NOK','USD','CHF'];
        if($country != '') {
            $currency = $currency[$country];
        }
        return $currency;
    }

    /** 
     * Perform Curl of Given Url 
     *
     * @param string $url URL 
     * @return Array $data
     */ 
    public function doCurl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = json_decode(curl_exec($ch), true);
        curl_close($ch);
        return $data;
    }

    public static function getWeekDates($year, $week) {
        $from = date("Y-m-d", strtotime("{$year}-W{$week}-1")); //Returns the date of monday in week
        $to = date("Y-m-d", strtotime("{$year}-W{$week}-7")); //Returns the date of sunday in week

        return ['week_start' => $from, 'week_end' => $to];
    }

    /**
    * Currency Convert
    *
    * @param int $from   Currency Code From
    * @param int $to     Currency Code To
    * @param int $price  Price Amount
    * @return int Converted amount
    */
    public function currency_convert($from = '', $to = '', $price = 0)
    {
        if($from == $to) {
            return number_format($price, 2, '.', '');
        }

        $rate = Currency::whereCode($from)->first()->rate;
        $session_rate = Currency::whereCode($to)->first()->rate;

        $usd_amount = $price / $rate;
        return number_format($usd_amount * $session_rate, 2, '.', '');
    }
}
