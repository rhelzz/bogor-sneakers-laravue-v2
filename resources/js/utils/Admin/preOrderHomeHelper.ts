export const getStatusClass = (status: string) => {
    switch (status) {
        case 'buka':
            return 'bg-emerald-100 text-emerald-700';
        case 'hampir_habis':
            return 'bg-amber-100 text-amber-700';
        case 'habis':
            return 'bg-rose-100 text-rose-700';
        default:
            return 'bg-slate-100 text-slate-700';
    }
};

export const formatStatus = (status: string) => {
    switch (status) {
        case 'buka':
            return 'Buka';
        case 'hampir_habis':
            return 'Hampir Habis';
        case 'habis':
            return 'Habis';
        default:
            return status;
    }
};

export const getSlotColor = (filled: number, max: number) => {
    if (!max || max <= 0) return 'bg-slate-500';
    const percentage = (filled / max) * 100;
    if (percentage >= 100) return 'bg-rose-500';
    if (percentage >= 80) return 'bg-amber-500';
    return 'bg-emerald-500';
};

export const formatDate = (dateStr: string | null) => {
    if (!dateStr) return '-';
    const date = new Date(dateStr);
    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(date);
};