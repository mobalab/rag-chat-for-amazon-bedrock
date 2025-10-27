=== RAG Chat for Amazon Bedrock ===
Author URI: https://github.com/mobalab
Plugin URI: https://github.com/mobalab/rag-chat-for-amazon-bedrock
Description: Integrate WordPress content with Amazon Bedrock Knowledge Bases to create an intelligent RAG chatbot system
Donate link: 
Contributors: 
Tags: rag, ai, amazon bedrock, chatbot, knowledge base
Requires at least: 6.6
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 0.0.1
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Integrate WordPress content with Amazon Bedrock Knowledge Bases to create an intelligent RAG (Retrieval-Augmented Generation) chatbot system.

== Description ==

RAG Chat for Amazon Bedrock is a WordPress plugin that seamlessly integrates your WordPress content with Amazon Bedrock Knowledge Bases to create an intelligent RAG (Retrieval-Augmented Generation) chatbot system.

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

**WordPress Installation:**
1. Install the plugin through the WordPress plugins screen directly, or upload the plugin files to the `/wp-content/plugins/rag-chat-ab` directory
2. Activate the plugin through the 'Plugins' screen in WordPress

**AWS Setup (Required):**
Before using this plugin, you need to set up Amazon Bedrock and create a Knowledge Base:
1. Create an AWS account if you don't have one: [AWS Account Setup](https://aws.amazon.com/resources/create-account/)
2. Set up Amazon Bedrock access: [Getting Started with Amazon Bedrock](https://docs.aws.amazon.com/bedrock/latest/userguide/getting-started.html)
3. Create a Knowledge Base: [Amazon Bedrock Knowledge Base Setup](https://docs.aws.amazon.com/bedrock/latest/userguide/knowledge-base.html)
4. Create a data source of "Custom" type within your Knowledge Base (required for the plugin to sync WordPress content)
5. Create an IAM user with the following permissions:
   - `bedrock:IngestKnowledgeBaseDocuments` (to sync WordPress content)
   - `bedrock:DeleteKnowledgeBaseDocuments` (to remove deleted WordPress content)
   - `bedrock:RetrieveAndGenerate` (to generate chat responses)
   - See [IAM Permissions for Amazon Bedrock](https://docs.aws.amazon.com/bedrock/latest/userguide/security_iam_service-with-iam.html) for detailed setup
6. Create AWS credentials (Access Key ID and Secret Access Key): [Managing AWS Access Keys](https://docs.aws.amazon.com/IAM/latest/UserGuide/id_credentials_access-keys.html)

**Plugin Configuration:**
1. Go to the RAG Chat admin menu to configure your Amazon Bedrock settings
2. Enter your AWS credentials and Knowledge Base details
3. Your content will automatically sync to Amazon Bedrock
4. Insert the shortcode `[rag_chat_ab_chat]` where you want the chat interface to appear
5. (Optional) Configure your chat interface settings for custom styling and behavior

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

= Do you offer professional setup and integration services? =

Yes! While this plugin is free to use, we understand that setting up AWS and Amazon Bedrock can be complex. We offer professional integration services to help you get your RAG system up and running smoothly. For more information about our integration services, please contact us at [https://tally.so/r/3jjoga](https://tally.so/r/3jjoga)

== External services ==

This plugin uses the following Amazon Bedrock API endpoints provided by Amazon Web Services, Inc.:

* PUT https://bedrock-agent.{$region}.amazonaws.com/knowledgebases/{$knowledge_base_id}/datasources/{$data_source_id}
    * Called when a post or page is created or updated.
    * Used to add / update content on Amazon Bedrock.
    * The whole content (post or page) is sent.
* POST https://bedrock-agent.{$region}.amazonaws.com/knowledgebases/{$knowledge_base_id}/datasources/{$data_source_id}/documents/deleteDocuments
    * Called when a post is deleted or moved to trash.
    * Used to delete content from Amazon Bedrock.
    * Only the post / page ID is sent.
* POST https://bedrock-agent-runtime.{$region}.amazonaws.com/retrieveAndGenerate
    * Called when a visitor asks a question.
    * Used to generate responses using Amazon Bedrock's AI.
    * The query that the user enters is sent.
    * The response contains a session ID, and it will be send to this endpoint in the subsequent requests along with the query.

Please see the following links for more information about Amazon Web Services:

* [AWS Service Terms](https://aws.amazon.com/service-terms/)
* [AWS Privacy](https://aws.amazon.com/privacy/)

== Related Projects ==

This plugin is based on **WP RAG** (https://github.com/mobalab/wp-rag), which provides RAG functionality using external servers and OpenAI's APIs. RAG Chat for Amazon Bedrock offers a different approach by integrating directly with Amazon Bedrock Knowledge Bases for users who prefer AWS-native solutions.

== Changelog ==

= 0.0.1: October 7th, 2025 =
* Birthday of RAG Chat for Amazon Bedrock!