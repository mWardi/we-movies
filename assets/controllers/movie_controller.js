import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

    static targets = ['modal', 'search'];

    connect() {
        console.log('Movie Controller');
        $('.search-input').autoComplete();
    }

    async openModal(event) {
        let modal = this.modalTarget;
        modal.innerHTML = await $.ajax({
            type: "GET",
            url: $(event.target).data('link')
        });
    }
}
