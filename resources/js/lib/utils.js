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