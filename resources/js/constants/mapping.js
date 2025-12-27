import { BOOKING_STATUS } from "./types";

export const BOOKING_STATUS_CLASS_MAPPING = {
    [BOOKING_STATUS.NEW]: 'new',
    [BOOKING_STATUS.CONFIRMED]: 'confirmed',
    [BOOKING_STATUS.IN_PROGRESS]: 'in-progress',
    [BOOKING_STATUS.COMPLETED]: 'completed',
    [BOOKING_STATUS.CANCELLED]: 'cancelled',
    [BOOKING_STATUS.PENDING_PAYMENT]: 'pending-payment',
    [BOOKING_STATUS.PAID]: 'paid',
};