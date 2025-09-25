=== WP RAG ===
Author URI: https://github.com/mobalab
Plugin URI: https://github.com/mobalab/wp-rag-for-amazon-bedrock
Description:
Donate link: 
Contributors: 
Tags: rag, ai
Requires at least: 6.6.0
Tested up to: 6.6.2
Requires PHP: 
Stable tag: 0.0.1
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

A WordPress plugin for building RAG using Amazon Bedrock.

== Description ==

This plugin allows you to build a RAG (Retrieval-Augmented Generation) system using your WordPress posts and pages.

Once enabled, our external server retrieves your posts and pages via the WordPress API,
calculates embeddings, and stores them in its vector database.

The plugin can display a chat dialog on your site. When a user (whether a guest or
a WordPress user) enters a query, it is sent to our external server. The server then
calculates embeddings, searches for relevant documents in the database, sends both
the user query and the retrieved documents to a generative AI, and returns the
generated response to the plugin.

Currently, the plugin only supports OpenAI's Embedding API and Chat API, so you'll
need an OpenAI API key to use this plugin.

== Frequently Asked Questions ==

= Is the plugin free? =

Yes, it's currently free, but we plan to transition to a freemium model in the future.

== Installation ==

TODO

== Changelog ==

= 0.0.1: TBD, 2025 =
* Birthday of WP RAG for Amazon Bedrock!