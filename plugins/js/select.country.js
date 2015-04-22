jQuery.fn.selectCountry = function(option){
    var defaults = {
        cityName: 'city_id',
        city: [],
        appendWhere: $(this).parent().find('.cityArea'),
        style: ''
    };
    // merge
    var options = $.extend({}, defaults, option);
    var city = options.city;
    var apEl = options.appendWhere;
    var country = $(this);
    var dCity;

    if(city.length != 0){
        var firstCountry = city[country.val()];
        if(apEl.find('.defaultCity').length != -1){
            dCity = apEl.find('.defaultCity').html();
        }

        var el = $(this).cityElement({
            cityName: options.cityName,
            firstCountry: firstCountry,
            defaultCity: dCity,
            style: options.style
        });
        apEl.html(el);

        country.change(function(e){
            var firstCountry = city[$(this).val()];
            el = $(this).cityElement({
                cityName: options.cityName,
                firstCountry: firstCountry,
                style: options.style
            });
            apEl.html(el);
        });
    }
};

jQuery.fn.cityElement = function(option){
    var defaults = {
        cityName: 'city_id',
        defaultCity: '',
        firstCountry: [],
        style: ''
    };
    // merge
    var options = $.extend({}, defaults, option);
    var firstCountry = options.firstCountry;

    var el = '';

    el += '<select name="' + options.cityName + '" class="required" ';
    el += options.style ? 'style="' + options.style + '"' : '';
    el += '>' + "\r\n";
    for(var this_id in firstCountry){
        var fc = firstCountry[this_id];
        for(var city_id in fc){
            el += "\t" + '<option value="' + city_id + '"';
            if(options.defaultCity){
                if(options.defaultCity == city_id){
                    el += ' selected="selected"';
                }
            }
            el += '>' + fc[city_id] + '</option>' + "\r\n";
        }
    }
    el += '</select>' + "\r\n";

    return el;
};