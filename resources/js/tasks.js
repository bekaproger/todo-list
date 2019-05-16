$('.tasks-finish-button').click(function(e){
    e.preventDefault();
    const url = $(this).attr('data-url');
    axios.post(url).then((e) => {
        if($(this).hasClass('btn-success')){
            $(this).addClass('btn-primary');
            $(this).removeClass('btn-success').text('Finish');
        }else{
            $(this).removeClass('btn-primary').addClass('btn-success').text('Finished');
        }
    }).catch((err) => {
        showAxiosError(err.response.errors.error)
    })
});


function showAxiosError(text) {
    $('#axios-error').text(text).css('display', 'block');
    let time = setTimeout(function () {
        $('#axios-error').css('display','none').text('');
        clearTimeout(time);
    }, 5000)
}