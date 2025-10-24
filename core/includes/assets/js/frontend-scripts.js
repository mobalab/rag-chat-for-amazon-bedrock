/*-------------------------
Frontend related javascript
-------------------------*/

/**
 * HELPER COMMENT START
 *
 * This file contains all of the frontend related javascript.
 * With frontend, it is meant the WordPress site that is visible for every visitor.
 *
 * Since you added the jQuery dependency within the "Add JS support" module, you see down below
 * the helper comment a function that allows you to use jQuery with the commonly known notation: $('')
 * By default, this notation is deactivated since WordPress uses the noConflict mode of jQuery
 * You can also use jQuery outside using the following notation: jQuery('')
 *
 * Here's some jQuery example code you can use to fire code once the page is loaded: $(document).ready( function(){} );
 *
 * Using the ajax example, you can send data back and forth between your frontend and the
 * backend of the website (PHP to ajax and vice-versa).
 * As seen in the example below, we use the jQuery $.ajax function to send data to the WordPress
 * callback my_demo_ajax_call, which was added within the Wp_Rag_Run class.
 * From there, we process the data and send it back to the code below, which will then display the
 * example within the console of your browser.
 *
 * You can add the localized variables in here as followed: wprag.plugin_name
 * These variables are defined within the localization function in the following file:
 * core/includes/classes/class-rag-chat-ab-run.php
 *
 * HELPER COMMENT END
 */

(function ( $ ) {

	"use strict";

	function scrollToBottom(messages) {
		messages[0].scrollTop = messages[0].scrollHeight;
	}

	function showUserMessage(messages, userName, message) {
		const container = $( '<div class="rag-chat-ab-message rag-chat-ab-message--user"></div>' );
		container.append( $( '<div class="rag-chat-ab-message__author">' ).text( userName ) )
		container.append( $( '<div class="rag-chat-ab-message__text--user">' ).text( message ) );
		messages.append( container );
		scrollToBottom( messages );
	}

	function showBotMessage(messages, botName, message, contexts = null) {
		const container = $( '<div class="rag-chat-ab-message rag-chat-ab-message--bot"></div>' );
		container.append( $( '<div class="rag-chat-ab-message__author--bot">' ).text( botName ) )
		container.append( $( '<div class="rag-chat-ab-message__text--bot">' ).text( message ) );
		if (contexts !== null) {
			showContexts(container, contexts)
		}
		messages.append( container );
		scrollToBottom( messages );
	}

	function showContexts(container, contexts) {
		if (contexts.length === 0) {
			return;
		}

		const relatedInfoDiv = $( '<div class="rag-chat-ab-related"></div>' );

		const titleDiv = $( '<div class="rag-chat-ab-related__title"></div>' );
		titleDiv.append( '<span class="rag-chat-ab-related__icon">📖</span>' );
		titleDiv.append( '<span class="rag-chat-ab-related__text">Related info</span>' );
		relatedInfoDiv.append( titleDiv );

		const linksDiv  = $( '<div class="rag-chat-ab-related__links"></div>' );
		contexts.forEach(
			context => {
				const a = $( `<a href="${context.url}" target="_blank" class="rag-chat-ab-related__link"></a>` );
				a.append( '<span class="rag-chat-ab-related__link-icon">🔗</span>' );
				a.append( $( '<span class="rag-chat-ab-related__link-text"></span>' ).text( context.title ) );
				linksDiv.append(a);
			}
		)
		relatedInfoDiv.append( linksDiv );
		container.append( relatedInfoDiv );
	}

	$( document ).ready(
		function () {
			const chatWindow     = $( '#rag-chat-ab-chat-window' );
			const chatIcon       = $( '#rag-chat-ab-chat-icon' );
			const form           = $( '#rag-chat-ab-chat-form' );
			const input          = $( '#rag-chat-ab-chat-input' );
			const submitButton   = form.find( '.rag-chat-ab-chat__submit' );
			const messages       = $( '#rag-chat-ab-chat-messages' );
			const minimizeButton = $( '.rag-chat-ab-chat__minimize' );
			const clearButton    = $( '.rag-chat-ab-chat__clear' );

			const userName       = ragChatAb.chat_ui_options['user_name'] || 'You';
			const botName        = ragChatAb.chat_ui_options['bot_name'] || 'Bot';
			const initialMessage = ragChatAb.chat_ui_options['initial_message'];

			let sessionId = null;

			function clearChatHistory() {
				messages.empty();
				sessionId = null;
				if ( initialMessage ) {
					showBotMessage( messages, botName, initialMessage );
				}
			}

			if ( initialMessage ) {
				showBotMessage( messages, botName, initialMessage );
			}

			const isMinimized = localStorage.getItem( 'rag-chat-ab-chat-minimized' ) === 'true';
			if (isMinimized) {
				chatWindow.addClass( 'rag-chat-ab--hidden' );
				chatIcon.removeClass( 'rag-chat-ab--hidden' );
			}
			minimizeButton.on(
				'click',
				function () {
					chatWindow.addClass( 'rag-chat-ab--hidden' );
					chatIcon.removeClass( 'rag-chat-ab--hidden' );
					localStorage.setItem( 'rag-chat-ab-chat-minimized', 'true' );
				}
			);
			chatIcon.on(
				'click',
				function () {
					chatWindow.removeClass( 'rag-chat-ab--hidden' );
					chatIcon.addClass( 'rag-chat-ab--hidden' );
					localStorage.setItem( 'rag-chat-ab-chat-minimized', 'false' );
					input.focus();
				}
			);

			clearButton.on(
				'click',
				function () {
					if (confirm('Are you sure you want to clear the chat history?')) {
						clearChatHistory();
					}
				}
			);

			form.on(
				'submit',
				function (e) {
					e.preventDefault();
					const message = $( '#rag-chat-ab-chat-input' ).val();

					if (message.trim() === '') {
						return;
					}

					submitButton.prop( 'disabled', true ).addClass( 'rag-chat-ab-chat__submit--loading' );

					const ajaxData = {
						action: 'rag_chat_ab_process_chat',
						message: message,
						nonce: ragChatAb.security_nonce
					};

					if (sessionId) {
						ajaxData.session_id = sessionId;
					}

					$.ajax(
						{
							url: ragChatAb.ajaxurl,
							type: 'POST',
							data: ajaxData,
							success: function (response) {
								if (response.success) {
									if (response.data.session_id) {
										sessionId = response.data.session_id;
									}

									showUserMessage( messages, userName, message );
									if ('yes' === ragChatAb.chat_ui_options['display_context_links']) {
										showBotMessage( messages, botName, response.data.answer, response.data.contexts );
									} else {
										showBotMessage( messages, botName, response.data.answer );
									}
								} else {
									messages.append( '<p><strong>Error:</strong> ' + response.data + '</p>' );
									scrollToBottom( messages );
								}
							},
							error: function (jqXHR) {
								var errorMessage = 'Unable to process your request.';
								if ( jqXHR.responseJSON.data && jqXHR.responseJSON.data.message ) {
									errorMessage = jqXHR.responseJSON.data.message;
								}

								messages.append( '<p><strong>Error:</strong> ' + errorMessage + '</p>' );
								scrollToBottom( messages );
							},
							complete: function () {
								input.val( '' ).focus();
								submitButton.prop( 'disabled', false ).removeClass( 'rag-chat-ab-chat__submit--loading' );
							}
						}
					);
				}
			);
		}
	);
})( jQuery );
