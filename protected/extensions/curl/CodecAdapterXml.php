<?php


class CodecAdapterXml extends CodecAdapterAbstract
{

    /**
     * Xml szovegge alakitja a megadott tombot.
     *
     * @param array $text   A kodolando tomb
     *
     * @return string
     */
    public function encode($text)
    {
        $xml = new SimpleXMLElement('<root/>');
        $tmp = (array)$text;
        array_walk_recursive($tmp, array($xml, 'addChild'));
        return $xml->asXML();
    }

    /**
     * Tombber alakitja a megadott szoveget.
     *
     * @param string $text   A dekodolando szoveg.
     *
     * @return array
     */
    public function decode($text)
    {
        $result = array();

        if (!empty($text)) {
            $xml = simplexml_load_string($text);
            if ($xml) {
                $this->convertXmlObjToArr($xml, $result);
            }
        }
        return $result;
    }

    /**
     * Rekurziv XML feldolgozo
     *
     * @param SimpleXmlElement $obj     Az xml objektum adott eleme
     * @param array $arr                A tomb, amibe az elemek bekerulnek
     *
     * @return void
     */
    private function convertXmlObjToArr(SimpleXmlElement $obj, &$arr)
    {
        if ($obj instanceof SimpleXMLElement && $obj->count()) {
            $children = $obj->children();
            foreach ($children as $elementName => $node) {
                $nextIdx = count($arr);
                $arr [$nextIdx] = array();
                $arr [$nextIdx] ['@name'] = strtolower((string)$elementName);
                $arr [$nextIdx] ['@attributes'] = array();
                $attributes = $node->attributes();
                foreach ($attributes as $attributeName => $attributeValue) {
                    $attribName = strtolower(trim((string)$attributeName));
                    $attribVal = trim((string)$attributeValue);
                    $arr [$nextIdx] ['@attributes'] [$attribName] = $attribVal;
                }
                $text = (string)$node;
                $text = trim($text);
                if (strlen($text) > 0) {
                    $arr [$nextIdx] ['@text'] = $text;
                }
                $arr [$nextIdx] ['@children'] = array();
                $this->convertXmlObjToArr($node, $arr [$nextIdx] ['@children']);
            }
        }
        return;
    }
}
