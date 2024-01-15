const timestamp = {
    hour: "2-digit",
    minute: "numeric",
    second: "numeric",
};

const dateDefaultOptions = {
    year: "numeric",
    month: "long",
    day: "2-digit",
};

const numberDefaultOptions = { 
    style:'decimal', 
    maximumFractionDigits:2
}


export const formatDate = function(locale = undefined, date = '', format="timestamp" ) {
    
    if(!date){
        return null;
    }
    const newOptions = format == 'date' ? dateDefaultOptions : {...dateDefaultOptions, ...timestamp};

    return new Intl.DateTimeFormat(locale,newOptions).format(new Date(date))
}
export const formatCurrency = function (locale =undefined, amount = '0.00',  options = {}) {
    const style = Object.keys(options).length === 0  ? numberDefaultOptions : {...numberDefaultOptions, ...options};
    return new Intl.NumberFormat(locale,style).format(amount)
}

export const titleCase = function(word){
    return word ? word.charAt(0).toUpperCase() + word.slice(1) : '';
}