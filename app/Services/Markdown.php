<?php namespace App\Services\Markdown;

use League\HTMLToMarkdown\HtmlConverter;
use Parsedown;

class Markdown
{
    protected $htmlConverter;
    protected $markdownParser;

    public function __construct()
    {
        $this->htmlConverter = new HtmlConverter(['header_style' => 'atx']);
        $this->markdownParser = new Parsedown();
    }

    public function convertHtmlToMarkdown($html)
    {
        return $this->htmlConverter->convert($html);
    }

    public function convertMarkdownToHtml($markdown)
    {
        $convertedHmtl = $this->markdownParser->text($markdown);
        return clean($convertedHmtl, 'user_topic_body');
    }
}
