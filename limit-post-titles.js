(function(){
	let Limiter = function(){
		let limiter = {
			limit: window.am_post_titles.limit,
			bootstrap: function(){
				this.el = document.getElementById('titlewrap');
				this.titleInput = this.el.getElementsByTagName('input')[0];
				if(this.el) {
					this.appendCounter();
					this.run();
				}
			},
			cutString: function(e) {
				let value = e.target.value.substr(0, this.limit)
				this.titleInput.value = value
			},
			checkLimit: function(e){
				if( this.titleInput.value.length > this.limit ) {
					this.cutString(e);
					e.stopImmediatePropagation();
					e.preventDefault();
					return false;
				}
			},
			appendCounter: function(){
				this.counter = document.createElement('span');
				this.counter.appendChild(document.createTextNode(this.limit));
				this.counter.id = 'am-title-limiter';
				this.el.appendChild(this.counter);
			},
			run: function(){
				this.titleInput.addEventListener('change', this.checkLimit.bind(this));
			}
		};
		this.init = function(){
			limiter.bootstrap();
		};
	};

	// Call on page load
	document.addEventListener("DOMContentLoaded",function(event){
		const limiter = new Limiter();
		limiter.init();
	});
})();
