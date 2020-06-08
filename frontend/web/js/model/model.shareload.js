var ShareLoad = (function(win){
    'use strict';

    var cont;
    var loadDiv;

    var loadStart = function(){
        cont.hide();
        loadDiv = $('<div class="shareload"><div class="shareloadimg"></div></div>');
        $('body').append(loadDiv);
    };
    var loadEnd = function(){
        cont.show();
        loadDiv.remove();
    };
    return {
        init: function($el){ win['ShareLoad'] = this; cont = $el; },
        loadEnd: function(){ loadEnd(); },
        loadStart: function(){ loadStart(); }
    }
})(window || {});