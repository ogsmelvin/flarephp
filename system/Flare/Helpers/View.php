<?php

if(!function_exists('html_select_day')){

    /**
     * 
     * @author anthony
     * @return string
     */
    function html_select_day()
    {
        $string = "<option value=''>Day</option>";
        foreach(range(1, 31) as $day){
            $string .= "<option value='".str_pad($day, 2, "0", STR_PAD_LEFT)."'>{$day}</option>";
        }
        return $string;
    }
}


if(!function_exists('html_select_month')){

    /**
     * 
     * @author anthony
     * @return string
     */
    function html_select_month()
    {
        return "<option value=''>Month</option>\n
        <option value='01'>Jan</option>\n
        <option value='02'>Feb</option>\n
        <option value='03'>Mar</option>\n
        <option value='04'>Apr</option>\n
        <option value='05'>May</option>\n
        <option value='06'>June</option>\n
        <option value='07'>July</option>\n
        <option value='08'>Aug</option>\n
        <option value='09'>Sept</option>\n
        <option value='10'>Oct</option>\n
        <option value='11'>Nov</option>\n
        <option value='12'>Dec</option>";
    }
}

if(!function_exists('html_select_province')){

    /**
     * 
     * @author anthony
     * @return string
     */
    function html_select_province()
    {
        return "<option value=''>Select Province</option>" . 
        "<option value='Abra'>Abra</option>" .
        "<option value='AgusandelNorte'>Agusan del Norte</option>" .
        "<option value='AgusandelSur'>Agusan del Sur</option>" .
        "<option value='Aklan'>Aklan</option>" .
        "<option value='Albay'>Albay</option>" .
        "<option value='Antique'>Antique</option>" .
        "<option value='Apayao'>Apayao</option>" .
        "<option value='Aurora'>Aurora</option>" .
        "<option value='Basilan'>Basilan</option>" .
        "<option value='Bataan'>Bataan</option>" .
        "<option value='Batanes'>Batanes</option>" .
        "<option value='Batangas'>Batangas</option>" .
        "<option value='Benguet'>Benguet</option>" .
        "<option value='Biliran'>Biliran</option>" .
        "<option value='Bohol'>Bohol</option>" .
        "<option value='Bukidnon'>Bukidnon</option>" .
        "<option value='Bulacan'>Bulacan</option>" .
        "<option value='Cagayan'>Cagayan</option>" .
        "<option value='CamarinesNorte'>Camarines Norte</option>" .
        "<option value='CamarinesSur'>Camarines Sur</option>" .
        "<option value='Camiguin'>Camiguin</option>" .
        "<option value='Capiz'>Capiz</option>" .
        "<option value='Catanduanes'>Catanduanes</option>" .
        "<option value='Cavite'>Cavite</option>" .
        "<option value='Cebu'>Cebu</option>" .
        "<option value='CompostellaValley'>Compostela Valley</option>" .
        "<option value='Cotabato'>Cotabato</option>" .
        "<option value='DavaoDelNorte'>Davao del Norte</option>" .
        "<option value='DavaoDelSur'>Davao del Sur</option>" .
        "<option value='DavaoOriental'>Davao Oriental</option>" .
        "<option value='DinagatIslands'>Dinagat Islands</option>" .
        "<option value='EasternSamar'>Eastern Samar</option>" .
        "<option value='Guimaras'>Guimaras</option>" .
        "<option value='Ifugao'>Ifugao</option>" .
        "<option value='IlocosNorte'>Ilocos Norte</option>" .
        "<option value='IlocosSur'>Ilocos Sur</option>" .
        "<option value='Iloilo'>Iloilo</option>" .
        "<option value='Isabela'>Isabela</option>" .
        "<option value='Kalinga'>Kalinga</option>" .
        "<option value='LaUnion'>La Union</option>" .
        "<option value='Laguna'>Laguna</option>" .
        "<option value='LanaoDelNorte'>Lanao del Norte</option>" .
        "<option value='LanaoDelSur'>Lanao del Sur</option>" .
        "<option value='Leyte'>Leyte</option>" .
        "<option value='Maguindanao'>Maguindanao</option>" .
        "<option value='Marinduque'>Marinduque</option>" .
        "<option value='Masbate'>Masbate</option>" .
        "<option value='MetroManila'>Metro Manila</option>" .
        "<option value='MisamisOccidental'>Misamis Occidental</option>" .
        "<option value='MisamisOriental'>Misamis Oriental</option>" .
        "<option value='MountainProvince'>Mountain Province</option>" .
        "<option value='NegrosOccidental'>Negros Occidental</option>" .
        "<option value='NegrosOriental'>Negros Oriental</option>" .
        "<option value='NorthernSamar'>Northern Samar</option>" .
        "<option value='NuevaEcija'>Nueva Ecija</option>" .
        "<option value='NuevaVizcaya'>Nueva Vizcaya</option>" .
        "<option value='OccidentalMindoro'>Occidental Mindoro</option>" .
        "<option value='OrientalMindoro'>Oriental Mindoro</option>" .
        "<option value='Palawan'>Palawan</option>" .
        "<option value='Pampanga'>Pampanga</option>" .
        "<option value='Pangasinan'>Pangasinan</option>" .
        "<option value='Quezon'>Quezon</option>" .
        "<option value='Quirino'>Quirino</option>" .
        "<option value='Rizal'>Rizal</option>" .
        "<option value='Roblon'>Romblon</option>" .
        "<option value='Samar'>Samar</option>" .
        "<option value='Saranagani'>Sarangani</option>" .
        "<option value='ShariffKabunsuan'>Shariff Kabunsuan</option>" .
        "<option value='Siquijor'>Siquijor</option>" .
        "<option value='Sorsogon'>Sorsogon</option>" .
        "<option value='SouthCotabato'>South Cotabato</option>" .
        "<option value='SouthernLeyte'>Southern Leyte</option>" .
        "<option value='SultanKudarat'>Sultan Kudarat</option>" .
        "<option value='Sulu'>Sulu</option>" .
        "<option value='SurigaoDelNorte'>Surigao del Norte</option>" .
        "<option value='SurigaoDelSur'>Surigao del Sur</option>" .
        "<option value='Tarlac'>Tarlac</option>" .
        "<option value='TawiTawi'>Tawi-Tawi</option>" .
        "<option value='Zambales'>Zambales</option>" .
        "<option value='ZamboangaDelNorte'>Zamboanga del Norte</option>" .
        "<option value='ZamboangaDelSur'>Zamboanga del Sur</option>" .
        "<option value='ZamboangaSibugay'>Zamboanga Sibugay</option>";
    }
}

if(!function_exists('html_select_year')){

    /**
     * 
     * @author anthony
     * @return string
     */
    function html_select_year($start = 1994, $end = 1920)
    {
        $string = "<option value=''>Year</option>";
        foreach(range((int) $start, (int) $end) as $year){
            $string .= "<option value='{$year}'>{$year}</option>";
        }
        return $string;
    }
}
