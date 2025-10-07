# WP RAG for Amazon Bedrock

WP RAG for Amazon Bedrock is a WordPress plugin that seamlessly integrates your WordPress content with Amazon Bedrock Knowledge Bases to create an intelligent RAG (Retrieval-Augmented Generation) chatbot system.

## Features

- **Direct Amazon Bedrock Integration**: Connects directly to Amazon Bedrock Knowledge Bases without external servers
- **Automatic Content Synchronization**: Automatically syncs WordPress posts and pages to Amazon Bedrock when content is created, updated, or deleted
- **Real-time Chat Interface**: Provides a chat widget for visitors to interact with your content using AI
- **Comprehensive Admin Interface**: Four dedicated admin pages for configuration and content management
- **AWS Security**: Uses AWS Signature Version 4 authentication for secure API communication
- **Content Management**: Track sync status and manage which content is synchronized to Amazon Bedrock

## Requirements

- WordPress 6.6.0 or higher
- PHP 7.4 or higher
- AWS account with Amazon Bedrock access
- Amazon Bedrock Knowledge Base configured
- Valid AWS credentials (Access Key ID and Secret Access Key)

## Installation

1. Upload the plugin files to the `/wp-content/plugins/wp-rag-for-amazon-bedrock` directory, or install the plugin through the WordPress plugins screen directly
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Go to the WP RAG admin menu to configure your Amazon Bedrock settings
4. Enter your AWS credentials and Knowledge Base details
5. Configure your chat interface settings
6. Your content will automatically sync to Amazon Bedrock

## How It Works

1. Configure your AWS credentials and Amazon Bedrock Knowledge Base in the plugin settings
2. Your WordPress posts and pages are automatically synchronized to Amazon Bedrock
3. Visitors can use the chat interface to ask questions about your content
4. Amazon Bedrock retrieves relevant content and generates intelligent responses using your WordPress data

## Admin Interface

The plugin provides four main admin pages:

- **Main Settings**: Overview and general configuration
- **General Settings**: Basic plugin configuration options
- **Content Management**: View sync status and manage synchronized content
- **Chat UI Configuration**: Customize the chat interface appearance and behavior

## Professional Integration Services

While this plugin is free to use, we understand that setting up AWS and Amazon Bedrock can be complex. We offer professional integration services to help you get your RAG system up and running smoothly. 

**[Contact us for integration services](https://tally.so/r/3jjoga)**

## Related Projects

This plugin is based on [WP RAG](https://github.com/mobalab/wp-rag), which provides RAG functionality using external servers and OpenAI's APIs. WP RAG for Amazon Bedrock offers a different approach by integrating directly with Amazon Bedrock Knowledge Bases for users who prefer AWS-native solutions.

## Support

For technical support, feature requests, or bug reports, please [create an issue](https://github.com/mobalab/wp-rag-for-amazon-bedrock/issues).

## License

This WordPress plugin is distributed under the GPL v3 or later.

*Note: This plugin requires an AWS Account with Amazon Bedrock access.*