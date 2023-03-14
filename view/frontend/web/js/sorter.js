// Adds sorting direction as request parameter to the URl
function addParamToUrl(URL, elementId, requestParam)
{
    var option = document.getElementById(elementId);    
    window.location = URL + requestParam + '=' + option.value;
}
