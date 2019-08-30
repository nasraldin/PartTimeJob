<aside class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
    <div class="cs-user-filters">
        <div class="cs-candidate-inputs">
            <form class="side-loc-srch-form" method="get" data-ajaxurl="http://jobcareer.chimpgroup.com/wp-admin/admin-ajax.php">
                                <!-- end extra query string -->

                <div class="search-bar">
                    <i class="icon-search7"></i>
                    <input type="text" placeholder="By Name" class="form-control txt-field side-location-field" id="cs_candidatename" name="cs_candidatename">
                </div>
                <!-- location with radius -->
                                    <div class="job-side-location-field">
                                                <div id="cs-select-holder" class="select-location" data-locationadminurl="http://jobcareer.chimpgroup.com/wp-admin/admin-ajax.php">
                            <div class="cs_searchbox_div" data-locationadminurl="http://jobcareer.chimpgroup.com/wp-admin/admin-ajax.php"><input type="text" autocomplete="off" placeholder="All Locations" class="form-control cs_search_location_field" name=""><input type="hidden" class="search_keyword" name="location" value=""><div class="cs_location_autocomplete" style="width: 256px; left: 396.5px; top: 526px; display: none;"></div></div>                            <a id="location_redius_popup158401966" href="javascript:void(0);" class="location-btn pop"><i class="icon-target3"></i></a>
                                                            <div id="popup158401966" style="display:none;" class="select-popup">
                                    <a class="cs-location-close-popup" id="cs_close158401966"><i class="cs-color icon-times"></i></a>
                                    <p>Show With in</p>
                                    <div class="slider slider-horizontal" id=""><div class="slider-track"><div class="slider-track-low" style="left: 0px; width: 0%;"></div><div class="slider-selection" style="left: 0%; width: 40%;"></div><div class="slider-track-high" style="right: 0px; width: 60%;"></div><div class="slider-handle min-slider-handle round" tabindex="0" style="left: 40%;"></div><div class="slider-handle max-slider-handle round hide" tabindex="0" style="left: 0%;"></div></div><div class="tooltip tooltip-main top" style="left: 40%; margin-left: 0px;"><div class="tooltip-arrow"></div><div class="tooltip-inner">200</div></div><div class="tooltip tooltip-min top"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div><div class="tooltip tooltip-max top"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div></div><input id="ex6158401966" type="text" name="radius" data-slider-min="0" data-slider-max="500" data-slider-step="20" data-slider-value="200" data-value="200" value="200" style="display: none;">
                                    <script>
                                        jQuery(document).ready(function () {
                                            jQuery('#ex6158401966').slider().on('slideStop', function (ev) {
                                                this.form.submit();
                                            });
                                        });
                                    </script>


                                    <span id="ex6CurrentSliderValLabel"><span id="ex6SliderVal158401966">200</span>Miles</span>
                                                                            <p class="my-location">of <i class="cs-color icon-location-arrow"></i><a class="cs-color" onclick="cs_get_location(this)">My location</a></p>
                                                                        </div>
                                                        </div>

                        <input type="text" onchange="this.form.submit()" style="display:none;" class="cs-geo-location form-control txt-field geo-search-location" name="">
                        <div class="cs-undo-select" style="display:none;">
                            <i class="icon-times"></i>
                        </div>
                    </div>
                                </form>
        </div>
        <div class="cs-candidate-lastactivity">
            <div class="searchbox-heading"> <h5>Last Activity</h5> </div>
            <ul>
                <li class="cs-radio-btn"><a href="?posted=lasthour" onclick="cs_listing_content_load();">Last Hour</a></li>
                <li class="cs-radio-btn"><a href="?posted=last24" onclick="cs_listing_content_load();">Last 24 hours</a></li>
                <li class="cs-radio-btn"><a href="?posted=7days" onclick="cs_listing_content_load();">Last 7 days</a></li>
                <li class="cs-radio-btn"><a href="?posted=14days" onclick="cs_listing_content_load();">Last 14 days</a></li>
                <li class="cs-radio-btn"><a href="?posted=30days" onclick="cs_listing_content_load();">Last 30 days</a></li>
                <li class="cs-radio-btn"><a href="?posted=all" class="active" onclick="cs_listing_content_load();">All</a></li>
            </ul>
        </div>
        <div class="cs-candidate-specialisms">
            <div class="searchbox-heading"> <h5>specialisms</h5> </div>
            <form method="GET" id="frm_specialisms_list">
                                <ul class="specialism_list">

                    <li class="radio"><div class="checkbox checkbox-primary"><input type="radio" onclick="cs_listing_content_load();" onchange="javascript:frm_specialisms_list.submit();" id="checklist1" name="specialisms" value="accountancy"><label for="checklist1">Accountancy<span>(4)</span></label></div></li><li class="radio"><div class="checkbox checkbox-primary"><input type="radio" onclick="cs_listing_content_load();" onchange="javascript:frm_specialisms_list.submit();" id="checklist2" name="specialisms" value="banking"><label for="checklist2">Banking<span>(3)</span></label></div></li><li class="radio"><div class="checkbox checkbox-primary"><input type="radio" onclick="cs_listing_content_load();" onchange="javascript:frm_specialisms_list.submit();" id="checklist3" name="specialisms" value="charity-voluntary"><label for="checklist3">Charity &amp; Voluntary<span>(3)</span></label></div></li><li class="radio"><div class="checkbox checkbox-primary"><input type="radio" onclick="cs_listing_content_load();" onchange="javascript:frm_specialisms_list.submit();" id="checklist4" name="specialisms" value="digital-creative"><label for="checklist4">Digital &amp; Creative<span>(4)</span></label></div></li><li class="radio"><div class="checkbox checkbox-primary"><input type="radio" onclick="cs_listing_content_load();" onchange="javascript:frm_specialisms_list.submit();" id="checklist5" name="specialisms" value="estate-agency"><label for="checklist5">Estate Agency<span>(2)</span></label></div></li><li class="radio"><div class="checkbox checkbox-primary"><input type="radio" onclick="cs_listing_content_load();" onchange="javascript:frm_specialisms_list.submit();" id="checklist6" name="specialisms" value="graduate"><label for="checklist6">Graduate<span>(3)</span></label></div></li><li class="radio"><div class="checkbox checkbox-primary"><input type="radio" onclick="cs_listing_content_load();" onchange="javascript:frm_specialisms_list.submit();" id="checklist7" name="specialisms" value="it-contractor"><label for="checklist7">IT Contractor<span>(4)</span></label></div></li>                            <li>
                                <a data-target="#light" data-toggle="modal" href="#">More</a>
                            </li>
                            
                </ul>
            </form>

        </div>

                    <a class="cs-expand-filters "><i class="icon-minus8"></i> Collapse all Filters</a>
            <div class="accordion" id="accordion2">
                                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion2" href="#collapse11">
                                       Experience                                </a>
                            </div>
                            <div id="collapse11" class="accordion-body collapse ">
                                <div class="accordion-inner">
                                                                            <form method="get" name="frm_show_experience">
                                            <ul class="custom-listing">
                                                <li><a onclick="cs_listing_content_load();" class="text-capitalize " href="?show-experience=0---2-years">0 - 2 Years <span>(30)</span></a></li><li><a onclick="cs_listing_content_load();" class="text-capitalize " href="?show-experience=3---5-years">3 - 5 Years <span>(0)</span></a></li><li><a onclick="cs_listing_content_load();" class="text-capitalize " href="?show-experience=6---7-years">6 - 7 Years <span>(0)</span></a></li><li><a onclick="cs_listing_content_load();" class="text-capitalize " href="?show-experience=8---10-years">8 - 10 Years <span>(0)</span></a></li><li><a onclick="cs_listing_content_load();" class="text-capitalize " href="?show-experience=11---15-years">11 - 15 Years <span>(0)</span></a></li><li><a onclick="cs_listing_content_load();" class="text-capitalize " href="?show-experience=16---20-years">16 - 20 Years <span>(0)</span></a></li><li><a onclick="cs_listing_content_load();" class="text-capitalize " href="?show-experience=21---25-years">21 - 25 Years <span>(0)</span></a></li><li><a onclick="cs_listing_content_load();" class="text-capitalize " href="?show-experience=26---30-years">26 - 30 Years <span>(0)</span></a></li><li><a onclick="cs_listing_content_load();" class="text-capitalize " href="?show-experience=31---35-years">31 - 35 Years <span>(0)</span></a></li><li><a onclick="cs_listing_content_load();" class="text-capitalize " href="?show-experience=more-than-35...">More than 35... <span>(0)</span></a></li>
                                            </ul>
                                        </form>
                                                                        </div>
                            </div>
                        </div>                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion2" href="#collapse12">
                                       Age                                </a>
                            </div>
                            <div id="collapse12" class="accordion-body collapse ">
                                <div class="accordion-inner">
                                                                            <form method="get" name="frm_show_age">
                                            <ul class="custom-listing">
                                                <li><a onclick="cs_listing_content_load();" class="text-capitalize " href="?show-age=18---22-years">18 - 22 Years <span>(30)</span></a></li><li><a onclick="cs_listing_content_load();" class="text-capitalize " href="?show-age=23---27-years">23 - 27 Years <span>(0)</span></a></li><li><a onclick="cs_listing_content_load();" class="text-capitalize " href="?show-age=28---32-years">28 - 32 Years <span>(0)</span></a></li><li><a onclick="cs_listing_content_load();" class="text-capitalize " href="?show-age=33---37-years">33 - 37 Years <span>(0)</span></a></li><li><a onclick="cs_listing_content_load();" class="text-capitalize " href="?show-age=38---42-years">38 - 42 Years <span>(0)</span></a></li><li><a onclick="cs_listing_content_load();" class="text-capitalize " href="?show-age=43---47-years">43 - 47 Years <span>(0)</span></a></li><li><a onclick="cs_listing_content_load();" class="text-capitalize " href="?show-age=48---52-years">48 - 52 Years <span>(0)</span></a></li><li><a onclick="cs_listing_content_load();" class="text-capitalize " href="?show-age=53---57-years">53 - 57 Years <span>(0)</span></a></li><li><a onclick="cs_listing_content_load();" class="text-capitalize " href="?show-age=above-57-years">Above 57 Years <span>(0)</span></a></li>
                                            </ul>
                                        </form>
                                                                        </div>
                            </div>
                        </div>                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion2" href="#collapse16">
                                       Education Levels                                </a>
                            </div>
                            <div id="collapse16" class="accordion-body collapse ">
                                <div class="accordion-inner">
                                                                            <form method="get" name="frm_education_level">
                                            <ul class="custom-listing">
                                                
                                                            <li class="checkbox"><input type="checkbox" id="education-level_1" name="education-level" value="certificate" onclick="cs_listing_content_load();" onchange="javascript:frm_education_level.submit();">
								<label for="education-level_1">Certificate<span>(30)</span></label></li>
                                                            <li class="checkbox"><input type="checkbox" id="education-level_2" name="education-level" value="diploma" onclick="cs_listing_content_load();" onchange="javascript:frm_education_level.submit();">
								<label for="education-level_2">Diploma<span>(0)</span></label></li>
                                                            <li class="checkbox"><input type="checkbox" id="education-level_3" name="education-level" value="associate-degree" onclick="cs_listing_content_load();" onchange="javascript:frm_education_level.submit();">
								<label for="education-level_3">Associate Degree<span>(0)</span></label></li>
                                                            <li class="checkbox"><input type="checkbox" id="education-level_4" name="education-level" value="bachelor-degree-/-graduate-degree" onclick="cs_listing_content_load();" onchange="javascript:frm_education_level.submit();">
								<label for="education-level_4">Bachelor Degree<span>(0)</span></label></li>
                                                            <li class="checkbox"><input type="checkbox" id="education-level_5" name="education-level" value="master" onclick="cs_listing_content_load();" onchange="javascript:frm_education_level.submit();">
								<label for="education-level_5">Master’s Degree<span>(0)</span></label></li>
                                                            <li class="checkbox"><input type="checkbox" id="education-level_6" name="education-level" value="doctorate-degree" onclick="cs_listing_content_load();" onchange="javascript:frm_education_level.submit();">
								<label for="education-level_6">Doctorate Degree<span>(0)</span></label></li>
                                            </ul>
                                        </form>
                                                                        </div>
                            </div>
                        </div>            </div>
            
    </div>
</aside>