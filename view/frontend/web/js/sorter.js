// Adds sorting direction as request parameter to the URl
function addParamToUrl(currentURL, elementId, requestParamName)
{
    var option = document.getElementById(elementId);
    var optionValue = option.value;
    window.location = currentURL + '?' + requestParamName + '=' + optionValue;
}
