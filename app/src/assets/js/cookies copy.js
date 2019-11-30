import '../../node_modules/cookieconsent/src/cookieconsent';

window.$ = window.jQuery = jQuery;

export const cookiesUtil = {
	read() {
		const cookies = document.cookie.split(';');
		return cookies.map(cookie => cookie.split('=', 1)[0].trim());
	},
	delete(cookieId) {
		let cookies = document.cookie;
		return cookies = `${cookieId}=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/`;
	}
};


window.freundeCookie = function () {
	let enablePiwik = () => {
		console.log("Matomo enabled");
		_paq.push(['rememberConsentGiven']);
		_paq.push(['trackPageView']);
		_paq.push(['enableLinkTracking']);
	};

	let disablePiwik = () => {
		console.log("PIWIK disabled");
		_paq.push(['forgetConsentGiven']);
	};

	let enableCookies = function () {
		console.log("COOKIES enabled");
		didConsent = true;
	};

	let currentCookies = null;
	let didConsent = false;

	let disableCookies = function () {
		console.log("COOKIES disabled");
		// setTimeout(() => {
		// 	console.log("CHECK cookies");
		// 	if (didConsent) {
		// 		console.log("CHECK cookies - consented nothing to do now");
		// 		return;
		// 	}
		// 	if (didConsent || currentCookies === document.cookie) {
		// 		console.log("CHECK cookies nothing to do now");
		// 		return;
		// 	}
		//
		// 	cookiesUtil.read().filter(name => !name.match(/cookieconsent/)).forEach(name => {
		// 		console.log("REMOVE cookie: " + name);
		// 		cookiesUtil.delete(name);
		// 	});
		//
		// 	console.log("CHECK cookies work done");
		// 	currentCookies = document.cookie;
		// }, 60);

		_paq.push(['forgetConsentGiven']);
	};

	let onTypeAndConsent = function (type) {
		if (type == 'opt-in' && didConsent) {
			enablePiwik();
			enableCookies();
			return;
		}
		disablePiwik();
		disableCookies();
	};
	let hasConsented = (status, type) => {
		if (type == 'opt-in' && status == cookieconsent.status.dismiss) {
			return false;
		}
		if (type == 'opt-in' && status == cookieconsent.status.allow) {
			return true;
		}
		return false;
	};
	let onInitialize = function (status) {
		let type = this.options.type;
		didConsent = hasConsented(status, type);
		onTypeAndConsent(type);
	};

	let onStatusChange = function(status, chosenBefore) {
		let type = this.options.type;
		didConsent = hasConsented(status, type);
		if (status === chosenBefore) {
			return;
		}
		onTypeAndConsent(type);
	};

	let onRevokeChoise = function() {
		let type = this.options.type;
		onTypeAndConsent(type, false);
	};

	return {
		initFreundeCookie: (options) => {
			options['onInitialise'] = onInitialize;
			options['onStatusChange'] = onStatusChange;
			options['onRevokeChoice'] = onRevokeChoise;
			cookieconsent.initialise(options);
		}
	}
};
