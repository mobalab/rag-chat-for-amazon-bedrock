=== WP RAG for Amazon Bedrock ===
Author URI: https://github.com/mobalab
Plugin URI: https://github.com/mobalab/wp-rag-for-amazon-bedrock
Description: Integrate WordPress content with Amazon Bedrock Knowledge Bases to create an intelligent RAG chatbot system
Donate link: 
Contributors: 
Tags: rag, ai, amazon bedrock, chatbot, knowledge base
Requires at least: 6.6.0
Tested up to: 6.6.2
Requires PHP: 7.4
Stable tag: 0.0.1
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Integrate WordPress content with Amazon Bedrock Knowledge Bases to create an intelligent RAG (Retrieval-Augmented Generation) chatbot system.

== Description ==

WP RAG for Amazon Bedrock is a WordPress plugin that seamlessly integrates your WordPress content with Amazon Bedrock Knowledge Bases to create an intelligent RAG (Retrieval-Augmented Generation) chatbot system.

**Key Features:**

* **Direct Amazon Bedrock Integration**: Connects directly to Amazon Bedrock Knowledge Bases without external servers
* **Automatic Content Synchronization**: Automatically syncs WordPress posts and pages to Amazon Bedrock when content is created, updated, or deleted
* **Real-time Chat Interface**: Provides a chat widget for visitors to interact with your content using AI
* **Comprehensive Admin Interface**: Four dedicated admin pages for configuration and content management
* **AWS Security**: Uses AWS Signature Version 4 authentication for secure API communication
* **Content Management**: Track sync status and manage which content is synchronized to Amazon Bedrock

**How It Works:**

1. Configure your AWS credentials and Amazon Bedrock Knowledge Base in the plugin settings
2. Your WordPress posts and pages are automatically synchronized to Amazon Bedrock
3. Visitors can use the chat interface to ask questions about your content
4. Amazon Bedrock retrieves relevant content and generates intelligent responses using your WordPress data

**Requirements:**

* AWS account with Amazon Bedrock access
* Amazon Bedrock Knowledge Base configured
* Valid AWS credentials (Access Key ID and Secret Access Key)

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/wp-rag-for-amazon-bedrock` directory, or install the plugin through the WordPress plugins screen directly
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Go to the WP RAG admin menu to configure your Amazon Bedrock settings
4. Enter your AWS credentials and Knowledge Base details
5. Configure your chat interface settings
6. Your content will automatically sync to Amazon Bedrock

== Frequently Asked Questions ==

= What is Amazon Bedrock? =

Amazon Bedrock is a fully managed service that offers a choice of high-performing foundation models (FMs) from leading AI companies through a single API, along with a broad set of capabilities to build generative AI applications.

= Do I need an AWS account? =

Yes, you need an AWS account with access to Amazon Bedrock and a configured Knowledge Base to use this plugin.

= What content types are supported? =

Currently, the plugin supports WordPress posts and pages. Custom post types may be added in future versions.

= Is my content sent to external servers? =

Your content is synchronized directly to your Amazon Bedrock Knowledge Base. No external third-party servers are involved in the process.

= Can I control which content gets synchronized? =

Not at the moment, but this feature is planned for future versions.

== Changelog ==

= 0.0.1: October 7th, 2025 =
* Birthday of WP RAG for Amazon Bedrock!