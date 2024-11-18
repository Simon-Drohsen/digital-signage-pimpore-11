import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['clock', 'date'];
    static values = {
        hourLabel: {
            type: String,
            default: 'Uhr',
        },
    };

    connect() {
        this.setTime();

        setInterval(() => {
            this.setTime();
        }, 60000);
    }

    setTime() {
        const time = new Date();

        const weekday = time.toLocaleString('ch', { weekday: 'long' });
        const month = time.toLocaleString('ch', { month: 'long' });
        const hours = String(time.getHours()).padStart(2, '0');
        const minutes = String(time.getMinutes()).padStart(2, '0');

        const clock = `${hours}:${minutes} ${this.hourLabelValue}`;
        this.clockTarget.innerHTML = clock;

        const date = `${weekday}, ${time.getDate()}. ${month} ${time.getFullYear()}`;
        this.dateTarget.innerHTML = date;
    }
}
