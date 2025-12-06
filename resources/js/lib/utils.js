export function cn(...classes) {
    return classes
        .filter(Boolean)
        .join(' ')
        .trim();
}

export function normalizePhone(phone) {
    if (!phone) return '';
    return phone.replace(/\s|\(|\)/g, '');
}

export function formatPhone(value) {
    if (!value) return '';
    
    let digits = value.replace(/[^\d+]/g, '');
    
    if (!digits.startsWith('+380')) {
        digits = digits.replace(/^\+/, '');
        if (digits.startsWith('380')) {
            digits = '+' + digits;
        } else if (digits.startsWith('0')) {
            digits = '+380' + digits.substring(1);
        } else {
            digits = '+380' + digits;
        }
    }
    
    if (digits.length > 13) {
        digits = digits.substring(0, 13);
    }
    
    if (digits.length <= 4) {
        return digits;
    }
    
    const phoneDigits = digits.substring(4);
    
    let formatted = '+380';
    
    if (phoneDigits.length > 0) {
        formatted += ' (';
        formatted += phoneDigits.substring(0, 2);
        
        if (phoneDigits.length > 2) {
            formatted += ') ' + phoneDigits.substring(2, 5);
            
            if (phoneDigits.length > 5) {
                formatted += ' ' + phoneDigits.substring(5, 7);
                
                if (phoneDigits.length > 7) {
                    formatted += ' ' + phoneDigits.substring(7, 9);
                }
            }
        } else {
            formatted += ')';
        }
    }
    
    return formatted;
}

export function formatLicencePlate(value) {
    if (!value) {
        return '';
    }

    let cleaned = value.replace(/[^A-ZА-Я0-9]/gi, '').toUpperCase();
    
    cleaned = cleaned.substring(0, 8);
    
    let lettersPart1 = '';
    let numbersPart = '';
    let lettersPart2 = '';
    
    const allLetters = [];
    const allNumbers = [];
    
    for (let i = 0; i < cleaned.length; i++) {
        const char = cleaned[i];
        if (/[A-ZА-Я]/.test(char)) {
            allLetters.push(char);
        } else if (/[0-9]/.test(char)) {
            allNumbers.push(char);
        }
    }
    
    lettersPart1 = allLetters.slice(0, 2).join('');
    
    numbersPart = allNumbers.slice(0, 4).join('');
    
    if (allLetters.length > 2) {
        lettersPart2 = allLetters.slice(2, 4).join('');
    }
    
    let result = '';
    if (lettersPart1.length > 0) {
        result = lettersPart1;
        if (numbersPart.length > 0) {
            result += ' ' + numbersPart;
            if (lettersPart2.length > 0) {
                result += ' ' + lettersPart2;
            }
        }
    }
    
    return result;
}

export function pluralize(count, one, few, many) {
    if (typeof count !== 'number' || count < 0) {
        return many;
    }
    
    const mod10 = count % 10;
    const mod100 = count % 100;

    if (mod100 >= 11 && mod100 <= 14) {
        return many;
    }
    
    if (mod10 === 1) {
        return one;
    }
    
    if (mod10 >= 2 && mod10 <= 4) {
        return few;
    }
    
    return many;
}

export function formatPrice(price) {
    return new Intl.NumberFormat('uk-UA', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(price);
}