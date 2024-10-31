import { Controller } from '@hotwired/stimulus';
import.meta.stimulusFetch = 'eager';
import.meta.stimulusIdentifier = "countdown";

export default class extends Controller {
    static targets = ['months', 'days', 'daysWithMonths', 'hours', 'minutes', 'seconds'];
    static values = {
        dateTime: String,
    };

    connect() {
        this.setCountdown();

        this.interval = setInterval(() => {
            this.setCountdown();
        }, 1000);
    }

    setCountdown() {
        let deadline = new Date(this.dateTimeValue).getTime();

        const now = new Date().getTime();
        const t = deadline - now;

        let months = Math.floor(t/(1000*60*60*24*30));
        let days = Math.floor(t/(1000*60*60*24));
        let daysWithMonths = Math.floor(t/(1000*60*60*24)) - (months * 30);
        let hours = Math.floor((t%(1000*60*60*24))/(1000*60*60));
        let minutes = Math.floor((t%(1000*60*60))/(1000*60));
        let seconds = Math.floor((t%(1000*60))/1000);

        if (t < 1) {
            clearInterval(this.interval);
            months = '00';
            daysWithMonths = '00';
            days = '00';
            hours = '00';
            minutes = '00';
            seconds = '00';
        }

        if (this.hasMonthsTarget) {
            this.monthsTarget.innerHTML = this.formatDigits(months);
        }
        if (this.hasDaysWithMonthsTarget) {
            this.daysWithMonthsTarget.innerHTML = this.formatDigits(daysWithMonths);
        }
        if (this.hasDaysTarget) {
            this.daysTarget.innerHTML = this.formatDigits(days);
        }
        if (this.hasHoursTarget) {
            this.hoursTarget.innerHTML = this.formatDigits(hours);
        }
        if (this.hasMinutesTarget) {
            this.minutesTarget.innerHTML = this.formatDigits(minutes);
        }
        if (this.hasSecondsTarget) {
            this.secondsTarget.innerHTML = this.formatDigits(seconds);
        }
    }

    formatDigits(digits) {
        return digits.toLocaleString('de-ch', { minimumIntegerDigits: 2 });
    }
};
