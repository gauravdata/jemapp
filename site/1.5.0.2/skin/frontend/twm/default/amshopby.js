function amshopby_start(){
    
    var btn = $('amshopby-price-btn');
    if (Object.isElement(btn)){
        Event.observe(btn, 'click', amshopby_price_click_callback);
        Event.observe($('amshopby-price-from'), 'focus', amshopby_price_focus_callback);
        Event.observe($('amshopby-price-from'), 'keypress', amshopby_price_click_callback);
        Event.observe($('amshopby-price-to'), 'focus', amshopby_price_focus_callback);
        Event.observe($('amshopby-price-to'), 'keypress', amshopby_price_click_callback);
    }
    
    $$('a.amshopby-less', 'a.amshopby-more').each(function (a){
        a.observe('click', amshopby_toggle)
    }); 
} 

function amshopby_price_click_callback(evt){
    if (evt.type == 'keypress' && 13 != evt.keyCode)
        return;
    
    var numFrom = amshopby_price_format($('amshopby-price-from').value);
    var numTo   = amshopby_price_format($('amshopby-price-to').value);
  
    if ((numFrom < 0.01 && numTo < 0.01) || numFrom < 0 || numTo < 0)   
        return;   
        
    var url =  $('amshopby-price-url').value.gsub('price-from', numFrom).gsub('price-to', numTo);
    setLocation(url);
}

function amshopby_price_focus_callback(evt){
    var el = Event.findElement(evt, 'input');
    if (isNaN(parseFloat(el.value))){
        el.value = '';
    } 
}


function amshopby_price_format(num){
    num = parseFloat(num);
    if (isNaN(num))
        num = 0;
        
    return Math.round(num) + '.00';   
}

function amshopby_toggle(evt){
    var attr = Event.findElement(evt, 'a').id.substr(14);
    
    $$('.amshopby-attr-' + attr).each(function (a){
        a.toggle();
    });        
        
    $('amshopby-less-' + attr).toggle();
    $('amshopby-more-' + attr).toggle();
    
    return false;
}