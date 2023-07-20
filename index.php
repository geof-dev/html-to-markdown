<?php
require 'vendor/autoload.php';
use Symfony\Component\DomCrawler\Crawler;
use League\HTMLToMarkdown\HtmlConverter;

if (isset($_GET['url']) ) {
	$url = $_GET['url'];

	if (!filter_var($url, FILTER_VALIDATE_URL)) {
		echo 'Invalid URL format. Please provide a valid URL.';
		exit();
	}

	// Get HTML
	$html = file_get_contents($url);

	// Get the body node
	$crawler = new Crawler($html);
	$bodyNode = $crawler->filter('body');

	// Remove script nodes
	$bodyNode->filter('script')->each(function (Crawler $scriptNode) {
		$scriptNode->getNode(0)->parentNode->removeChild($scriptNode->getNode(0));
	});

	// Remove style nodes
	$bodyNode->filter('style')->each(function (Crawler $styleNode) {
		$styleNode->getNode(0)->parentNode->removeChild($styleNode->getNode(0));
	});

	// HTML content of the body node
	$bodyHtml = $bodyNode->html();

	// Convert to Markdown
	$converter = new HtmlConverter();
	$markdown = $converter->convert($bodyHtml);

	// delete all html balise
	$plainText = strip_tags($markdown);

	// export in an md file to test
	//file_put_contents('result.md', $plainText);

	header('Content-Type: text/markdown');
	echo $plainText;
} else {
	echo 'Please provide the "url" parameter in the query string.';
}
