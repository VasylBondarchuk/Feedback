// Adds sorting direction as request parameter to the URl
function addParamToUrl(currentURL, elementId, requestParamName)
{
    var option = document.getElementById(elementId);    
    window.location = currentURL + '?' + requestParamName + '=' + option.value;
}
