    $(document).on('click','#btn-contact',function() {
        let url =$.cookie("contact");
        url=encodeURIComponent(url);
        url = decodeURIComponent(url);
        window.open(url,"_blank")
    });
    
    $(document).on('click','#btn-hirePlan',function() {
        window.location.href="checkout.php";
        
    });
    $(document).on('click','#back',function() {
        window.location.href="index.php";
        
    });
    