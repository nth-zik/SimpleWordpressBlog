/*
 * Slider Settings
 */
jQuery(document).ready(function(){
   jQuery('.widget_slider_area_rotate').bxSlider({
      mode: 'horizontal',
      speed: 2000,
      auto: true,
      pause: 10000,
      adaptiveHeight: false,
      controls:true,
      nextText: '',
      //<div class="category-slide-next"><i class="fa fa-arrow-circle-right fa-3x" aria-hidden="true" style="color:black"></i></div>
      //<div class="category-slide-prev"><i class="fa fa-arrow-circle-left fa-3x" aria-hidden="true" style="color:black"></i></div>
      prevText: '',
      //nextSelector: '.slide-next',
      //prevSelector: '.slide-prev',
      pager: true,
      tickerHover: true,
      touchEnabled: true
   });
});