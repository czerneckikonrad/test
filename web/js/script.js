$(document).ready(function(){
    function player(id) {
        return '<div class="youtube-player" id="'+id+'"><iframe width="560" height="315" src="https://www.youtube.com/embed/'+id+'?rel=0&amp;showinfo=0&amp;autoplay=1" frameborder="0" allowfullscreen></iframe></div>';
    }

    var currVideo = false;

    $("body").on("click", "[data-video]", function(e){
        e.preventDefault();
        var $this = $(this);
        var id = $(this).attr("data-video");

        $(this).toggleClass("open");


        $('.youtube-player').not($this).slideUp("fast", function() { $(this).remove() });


        if(!$(this).hasClass("open")) {
            $(this).find('.youtube-player').slideUp("fast", function() { $(this).remove() });
        } else {
            $(this).after(player(id)).hide().slideToggle("normal");
        }
    });

});