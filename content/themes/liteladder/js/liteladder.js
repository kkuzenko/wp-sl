$(document).ready(function(){

    $('#sliderWrapper').bxSlider({
        slideSelector : '.slide',
        pager : false,
        auto: true,
        useCSS: false
    });
    var newsHeight = $('#newsCol').height() - 20;
    var twitterWidth = $('#twitterCol').width();
    console.log(newsHeight)
    twttr.ready(
        function (twttr) {
            twttr.widgets.createTimeline(
                '663533388515688448',
                document.getElementById('twitterCol'),
                {
                    width: twitterWidth,
                    height: newsHeight,
                    related: 'twitterdev,twitterapi',
                    chrome : 'transparent, noborders'
                }).then(function (el) {
                console.log("Embedded a timeline.")
            });

        }
    );

});

/*

 */