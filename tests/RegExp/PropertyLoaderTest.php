<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Test\RegExp;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\RegExp\Exception\InvalidPropertyConfigException;
use Remorhaz\UniLex\RegExp\Exception\InvalidPropertyRangeSetException;
use Remorhaz\UniLex\RegExp\Exception\PropertyFileNotLoadedException;
use Remorhaz\UniLex\RegExp\Exception\PropertyRangeSetNotFoundException;
use Remorhaz\UniLex\RegExp\FSM\RangeSet;
use Remorhaz\UniLex\RegExp\PropertyLoader;

use function preg_quote;

/**
 * @covers \Remorhaz\UniLex\RegExp\PropertyLoader
 */
class PropertyLoaderTest extends TestCase
{

    public function testGetRangeSet_RangeSetNotExists_ThrowsException(): void
    {
        $propertyLoader = new PropertyLoader(__DIR__, []);
        $this->expectException(PropertyRangeSetNotFoundException::class);
        $this->expectExceptionMessage("Range set not found for Unicode property 'a'");
        $propertyLoader->getRangeSet('a');
    }

    public function testGetRangeSet_InvalidRangeSet_ThrowsException(): void
    {
        $propertyLoader = new PropertyLoader(__DIR__, ['a' => (object) ['b' => 'c']]);
        $this->expectException(InvalidPropertyConfigException::class);
        $this->expectExceptionMessage("Invalid config for Unicode property 'a': object instead of string filename");
        $propertyLoader->getRangeSet('a');
    }

    public function testGetRangeSet_FileNotExists_ThrowsException(): void
    {
        $propertyLoader = new PropertyLoader(__DIR__, ['a' => '/NonExisting.php']);
        $this->expectException(PropertyFileNotLoadedException::class);
        $this->expectExceptionMessageMatches(
            "#^Failed to load range set for Unicode property 'a' from file .+/NonExisting\.php:\n.+\$#"
        );
        $propertyLoader->getRangeSet('a');
    }

    public function testGetRangeSet_FileExistsAndReturnsNotRangeSet_ThrowsException(): void
    {
        $propertyLoader = new PropertyLoader(__DIR__, ['a' => '/InvalidPropertyIndex.php']);
        $this->expectException(InvalidPropertyRangeSetException::class);
        $expectedClass = preg_quote(RangeSet::class, '#');
        $this->expectExceptionMessageMatches(
            "#^Invalid range set loaded from .+ for Unicode property 'a':\n" .
            "\\S+ instead of {$expectedClass}\$#"
        );
        $propertyLoader->getRangeSet('a');
    }

    /**
     * @param string $propertyName
     * @param int    $rangeIndex
     * @param int    $codePoint
     * @dataProvider providerGetRangeSet
     */
    public function testGetRangeSet_FileExistsAndReturnsRangeSet_ReturnsMatchingRange(
        string $propertyName,
        int $rangeIndex,
        int $codePoint
    ): void {
        $propertyLoader = PropertyLoader::create();
        $ranges = $propertyLoader
            ->getRangeSet($propertyName)
            ->getRanges();
        self::assertArrayHasKey($rangeIndex, $ranges);
        self::assertTrue($ranges[$rangeIndex]->containsChar($codePoint));
    }

    public function providerGetRangeSet(): array
    {
        return [
            'Adlam' => ['Adlam', 0, 0x01E900],
            'Ahom' => ['Ahom', 0, 0x011700],
            'Alphabetic' => ['Alphabetic', 0, 0x41],
            'Anatolian_Hieroglyphs' => ['Anatolian_Hieroglyphs', 0, 0x014400],
            'Any' => ['Any', 0, 0x00],
            'Arabic' => ['Arabic', 0, 0x0600],
            'Armenian' => ['Armenian', 0, 0x0531],
            'ASCII_Hex_Digit' => ['ASCII_Hex_Digit', 0, 0x30],
            'Avestan' => ['Avestan', 0, 0x010B00],
            'Balinese' => ['Balinese', 0, 0x1B00],
            'Bamum' => ['Bamum', 0, 0xA6A0],
            'Bassa_Vah' => ['Bassa_Vah', 0, 0x016AD0],
            'Batak' => ['Batak', 0, 0x1BC0],
            'Bengali' => ['Bengali', 0, 0x0980],
            'Bhaiksuki' => ['Bhaiksuki', 0, 0x011C00],
            'Bidi_Control' => ['Bidi_Control', 0, 0x061C],
            'Bopomofo' => ['Bopomofo', 0, 0x02EA],
            'Brahmi' => ['Brahmi', 0, 0x011000],
            'Braille' => ['Braille', 0, 0x2800],
            'Buginese' => ['Buginese', 0, 0x1A00],
            'Buhid' => ['Buhid', 0, 0x1740],
            'C' => ['C', 0, 0x00],
            'Canadian_Aboriginal' => ['Canadian_Aboriginal', 0, 0x1400],
            'Carian' => ['Carian', 0, 0x0102A0],
            'Case_Ignorable' => ['Case_Ignorable', 0, 0x27],
            'Cased' => ['Cased', 0, 0x41],
            'Caucasian_Albanian' => ['Caucasian_Albanian', 0, 0x010530],
            'Cc' => ['Cc', 0, 0x00],
            'Cf' => ['Cf', 0, 0xAD],
            'Chakma' => ['Chakma', 0, 0x011100],
            'Cham' => ['Cham', 0, 0xAA00],
            'Changes_When_Casefolded' => ['Changes_When_Casefolded', 0, 0x41],
            'Changes_When_Casemapped' => ['Changes_When_Casemapped', 0, 0x41],
            'Changes_When_Lowercased' => ['Changes_When_Lowercased', 0, 0x41],
            'Changes_When_Titlecased' => ['Changes_When_Titlecased', 0, 0x61],
            'Changes_When_Uppercased' => ['Changes_When_Uppercased', 0, 0x61],
            'Cherokee' => ['Cherokee', 0, 0x13A0],
            'Chorasmian' => ['Chorasmian', 0, 0x010FB0],
            'Cn' => ['Cn', 0, 0x0378],
            'Co' => ['Co', 0, 0xE000],
            'Common' => ['Common', 0, 0x00],
            'Coptic' => ['Coptic', 0, 0x03E2],
            'Cs' => ['Cs', 0, 0xD800],
            'Cuneiform' => ['Cuneiform', 0, 0x012000],
            'Cypriot' => ['Cypriot', 0, 0x010800],
            'Cyrillic' => ['Cyrillic', 0, 0x0400],
            'Dash' => ['Dash', 0, 0x2D],
            'Default_Ignorable_Code_Point' => ['Default_Ignorable_Code_Point', 0, 0xAD],
            'Deprecated' => ['Deprecated', 0, 0x0149],
            'Deseret' => ['Deseret', 0, 0x010400],
            'Devanagari' => ['Devanagari', 0, 0x0900],
            'Diacritic' => ['Diacritic', 0, 0x5E],
            'Dives_Akuru' => ['Dives_Akuru', 0, 0x011900],
            'Dogra' => ['Dogra', 0, 0x011800],
            'Duployan' => ['Duployan', 0, 0x01BC00],
            'Egyptian_Hieroglyphs' => ['Egyptian_Hieroglyphs', 0, 0x013000],
            'Elbasan' => ['Elbasan', 0, 0x010500],
            'Elymaic' => ['Elymaic', 0, 0x010FE0],
            'Ethiopic' => ['Ethiopic', 0, 0x1200],
            'Extender' => ['Extender', 0, 0xB7],
            'Georgian' => ['Georgian', 0, 0x10A0],
            'Glagolitic' => ['Glagolitic', 0, 0x2C00],
            'Gothic' => ['Gothic', 0, 0x010330],
            'Grantha' => ['Grantha', 0, 0x011300],
            'Grapheme_Base' => ['Grapheme_Base', 0, 0x20],
            'Grapheme_Extend' => ['Grapheme_Extend', 0, 0x0300],
            'Grapheme_Link' => ['Grapheme_Link', 0, 0x094D],
            'Greek' => ['Greek', 0, 0x0370],
            'Gujarati' => ['Gujarati', 0, 0x0A81],
            'Gunjala_Gondi' => ['Gunjala_Gondi', 0, 0x011D60],
            'Gurmukhi' => ['Gurmukhi', 0, 0x0A01],
            'Han' => ['Han', 0, 0x2E80],
            'Hangul' => ['Hangul', 0, 0x1100],
            'Hanifi_Rohingya' => ['Hanifi_Rohingya', 0, 0x10D00],
            'Hanunoo' => ['Hanunoo', 0, 0x1720],
            'Hatran' => ['Hatran', 0, 0x0108E0],
            'Hebrew' => ['Hebrew', 0, 0x0591],
            'Hex_Digit' => ['Hex_Digit', 0, 0x30],
            'Hiragana' => ['Hiragana', 0, 0x3041],
            'Hyphen' => ['Hyphen', 0, 0x2D],
            'ID_Continue' => ['ID_Continue', 0, 0x30],
            'ID_Start' => ['ID_Start', 0, 0x41],
            'Ideographic' => ['Ideographic', 0, 0x3006],
            'IDS_Binary_Operator' => ['IDS_Binary_Operator', 0, 0x2FF0],
            'IDS_Trinary_Operator' => ['IDS_Trinary_Operator', 0, 0x2FF2],
            'Imperial_Aramaic' => ['Imperial_Aramaic', 0, 0x010840],
            'Inherited' => ['Inherited', 0, 0x0300],
            'Inscriptional_Pahlavi' => ['Inscriptional_Pahlavi', 0, 0x010B60],
            'Inscriptional_Parthian' => ['Inscriptional_Parthian', 0, 0x010B40],
            'Javanese' => ['Javanese', 0, 0xA980],
            'Join_Control' => ['Join_Control', 0, 0x200C],
            'Kaithi' => ['Kaithi', 0, 0x011080],
            'Kannada' => ['Kannada', 0, 0x0C80],
            'Katakana' => ['Katakana', 0, 0x30A1],
            'Kayah_Li' => ['Kayah_Li', 0, 0xA900],
            'Kharoshthi' => ['Kharoshthi', 0, 0x010A00],
            'Khitan_Small_Script' => ['Khitan_Small_Script', 0, 0x016FE4],
            'Khmer' => ['Khmer', 0, 0x1780],
            'Khojki' => ['Khojki', 0, 0x011200],
            'Khudawadi' => ['Khudawadi', 0, 0x0112B0],
            'L' => ['L', 0, 0x41],
            'L&' => ['L&', 0, 0x41],
            'Lao' => ['Lao', 0, 0x0E81],
            'Latin' => ['Latin', 0, 0x41],
            'Lepcha' => ['Lepcha', 0, 0x1C00],
            'Limbu' => ['Limbu', 0, 0x1900],
            'Linear_A' => ['Linear_A', 0, 0x010600],
            'Linear_B' => ['Linear_B', 0, 0x010000],
            'Lisu' => ['Lisu', 0, 0xA4D0],
            'Ll' => ['Ll', 0, 0x61],
            'Lm' => ['Lm', 0, 0x02B0],
            'Lo' => ['Lo', 0, 0xAA],
            'Logical_Order_Exception' => ['Logical_Order_Exception', 0, 0x0E40],
            'Lowercase' => ['Lowercase', 0, 0x61],
            'Lt' => ['Lt', 0, 0x01C5],
            'Lu' => ['Lu', 0, 0x41],
            'Lycian' => ['Lycian', 0, 0x010280],
            'Lydian' => ['Lydian', 0, 0x010920],
            'M' => ['M', 0, 0x0300],
            'Mahajani' => ['Mahajani', 0, 0x011150],
            'Makasar' => ['Makasar', 0, 0x011EE0],
            'Malayalam' => ['Malayalam', 0, 0x0D00],
            'Mandaic' => ['Mandaic', 0, 0x0840],
            'Manichaean' => ['Manichaean', 0, 0x010AC0],
            'Marchen' => ['Marchen', 0, 0x011C70],
            'Masaram_Gondi' => ['Masaram_Gondi', 0, 0x011D00],
            'Math' => ['Math', 0, 0x2B],
            'Mc' => ['Mc', 0, 0x0903],
            'Me' => ['Me', 0, 0x0488],
            'Medefaidrin' => ['Medefaidrin', 0, 0x016E40],
            'Meetei_Mayek' => ['Meetei_Mayek', 0, 0xAAE0],
            'Mende_Kikakui' => ['Mende_Kikakui', 0, 0x01E800],
            'Meroitic_Cursive' => ['Meroitic_Cursive', 0, 0x0109A0],
            'Meroitic_Hieroglyphs' => ['Meroitic_Hieroglyphs', 0, 0x010980],
            'Miao' => ['Miao', 0, 0x016F00],
            'Mn' => ['Mn', 0, 0x0300],
            'Modi' => ['Modi', 0, 0x011600],
            'Mongolian' => ['Mongolian', 0, 0x1800],
            'Mro' => ['Mro', 0, 0x016A40],
            'Multani' => ['Multani', 0, 0x011280],
            'Myanmar' => ['Myanmar', 0, 0x1000],
            'N' => ['N', 0, 0x30],
            'Nabataean' => ['Nabataean', 0, 0x010880],
            'Nandinagari' => ['Nandinagari', 0, 0x0119A0],
            'Nd' => ['Nd', 0, 0x30],
            'New_Tai_Lue' => ['New_Tai_Lue', 0, 0x1980],
            'Newa' => ['Newa', 0, 0x011400],
            'Nko' => ['Nko', 0, 0x07C0],
            'Nl' => ['Nl', 0, 0x16EE],
            'No' => ['No', 0, 0xB2],
            'Noncharacter_Code_Point' => ['Noncharacter_Code_Point', 0, 0xFDD0],
            'Nushu' => ['Nushu', 0, 0x016FE1],
            'Nyiakeng_Puachue_Hmong' => ['Nyiakeng_Puachue_Hmong', 0, 0x01E100],
            'Ogham' => ['Ogham', 0, 0x1680],
            'Ol_Chiki' => ['Ol_Chiki', 0, 0x1C50],
            'Old_Hungarian' => ['Old_Hungarian', 0, 0x010C80],
            'Old_Italic' => ['Old_Italic', 0, 0x010300],
            'Old_North_Arabian' => ['Old_North_Arabian', 0, 0x010A80],
            'Old_Persian' => ['Old_Persian', 0, 0x0103A0],
            'Old_Sogdian' => ['Old_Sogdian', 0, 0x010F00],
            'Old_South_Arabian' => ['Old_South_Arabian', 0, 0x010A60],
            'Old_Turkic' => ['Old_Turkic', 0, 0x010C00],
            'Oriya' => ['Oriya', 0, 0x0B01],
            'Osage' => ['Osage', 0, 0x0104B0],
            'Osmanya' => ['Osmanya', 0, 0x010480],
            'Other_Alphabetic' => ['Other_Alphabetic', 0, 0x0345],
            'Other_Default_Ignorable_Code_Point' => ['Other_Default_Ignorable_Code_Point', 0, 0x034F],
            'Other_Grapheme_Extend' => ['Other_Grapheme_Extend', 0, 0x09BE],
            'Other_ID_Continue' => ['Other_ID_Continue', 0, 0xB7],
            'Other_ID_Start' => ['Other_ID_Start', 0, 0x1885],
            'Other_Lowercase' => ['Other_Lowercase', 0, 0xAA],
            'Other_Math' => ['Other_Math', 0, 0x5E],
            'Other_Uppercase' => ['Other_Uppercase', 0, 0x2160],
            'P' => ['P', 0, 0x21],
            'Pahawh_Hmong' => ['Pahawh_Hmong', 0, 0x016B00],
            'Palmyrene' => ['Palmyrene', 0, 0x010860],
            'Pattern_Syntax' => ['Pattern_Syntax', 0, 0x21],
            'Pattern_White_Space' => ['Pattern_White_Space', 0, 0x09],
            'Pau_Cin_Hau' => ['Pau_Cin_Hau', 0, 0x011AC0],
            'Pc' => ['Pc', 0, 0x5F],
            'Pd' => ['Pd', 0, 0x2D],
            'Pe' => ['Pe', 0, 0x29],
            'Pf' => ['Pf', 0, 0xBB],
            'Phags_Pa' => ['Phags_Pa', 0, 0xA840],
            'Phoenician' => ['Phoenician', 0, 0x010900],
            'Pi' => ['Pi', 0, 0xAB],
            'Po' => ['Po', 0, 0x21],
            'Prepended_Concatenation_Mark' => ['Prepended_Concatenation_Mark', 0, 0x0600],
            'Ps' => ['Ps', 0, 0x28],
            'Psalter_Pahlavi' => ['Psalter_Pahlavi', 0, 0x010B80],
            'Quotation_Mark' => ['Quotation_Mark', 0, 0x22],
            'Radical' => ['Radical', 0, 0x2E80],
            'Regional_Indicator' => ['Regional_Indicator', 0, 0x01F1E6],
            'Rejang' => ['Rejang', 0, 0xA930],
            'Runic' => ['Runic', 0, 0x16A0],
            'S' => ['S', 0, 0x24],
            'Samaritan' => ['Samaritan', 0, 0x0800],
            'Saurashtra' => ['Saurashtra', 0, 0xA880],
            'Sc' => ['Sc', 0, 0x24],
            'Sentence_Terminal' => ['Sentence_Terminal', 0, 0x21],
            'Sharada' => ['Sharada', 0, 0x011180],
            'Shavian' => ['Shavian', 0, 0x010450],
            'Siddham' => ['Siddham', 0, 0x011580],
            'SignWriting' => ['SignWriting', 0, 0x01D800],
            'Sinhala' => ['Sinhala', 0, 0x0D81],
            'Sk' => ['Sk', 0, 0x5E],
            'Sm' => ['Sm', 0, 0x2B],
            'So' => ['So', 0, 0xA6],
            'Soft_Dotted' => ['Soft_Dotted', 0, 0x69],
            'Sogdian' => ['Sogdian', 0, 0x010F30],
            'Sora_Sompeng' => ['Sora_Sompeng', 0, 0x0110D0],
            'Soyombo' => ['Soyombo', 0, 0x011A50],
            'Sundanese' => ['Sundanese', 0, 0x1B80],
            'Syloti_Nagri' => ['Syloti_Nagri', 0, 0xA800],
            'Syriac' => ['Syriac', 0, 0x0700],
            'Tagalog' => ['Tagalog', 0, 0x1700],
            'Tagbanwa' => ['Tagbanwa', 0, 0x1760],
            'Tai_Le' => ['Tai_Le', 0, 0x1950],
            'Tai_Tham' => ['Tai_Tham', 0, 0x1A20],
            'Tai_Viet' => ['Tai_Viet', 0, 0xAA80],
            'Takri' => ['Takri', 0, 0x011680],
            'Tamil' => ['Tamil', 0, 0x0B82],
            'Tangut' => ['Tangut', 0, 0x016FE0],
            'Telugu' => ['Telugu', 0, 0x0C00],
            'Terminal_Punctuation' => ['Terminal_Punctuation', 0, 0x21],
            'Thaana' => ['Thaana', 0, 0x0780],
            'Thai' => ['Thai', 0, 0x0E01],
            'Tibetan' => ['Tibetan', 0, 0x0F00],
            'Tifinagh' => ['Tifinagh', 0, 0x2D30],
            'Tirhuta' => ['Tirhuta', 0, 0x011480],
            'Ugaritic' => ['Ugaritic', 0, 0x010380],
            'Unified_Ideograph' => ['Unified_Ideograph', 0, 0x3400],
            'Unknown' => ['Unknown', 0, 0x0378],
            'Uppercase' => ['Uppercase', 0, 0x41],
            'Vai' => ['Vai', 0, 0xA500],
            'Variation_Selector' => ['Variation_Selector', 0, 0x180B],
            'Wancho' => ['Wancho', 0, 0x01E2C0],
            'Warang_Citi' => ['Warang_Citi', 0, 0x0118A0],
            'White_Space' => ['White_Space', 0, 0x09],
            'XID_Continue' => ['XID_Continue', 0, 0x30],
            'XID_Start' => ['XID_Start', 0, 0x41],
            'Yezidi' => ['Yezidi', 0, 0x010E80],
            'Yi' => ['Yi', 0, 0xA000],
            'Z' => ['Z', 0, 0x20],
            'Zanabazar_Square' => ['Zanabazar_Square', 0, 0x011A00],
            'Zl' => ['Zl', 0, 0x2028],
            'Zp' => ['Zp', 0, 0x2029],
            'Zs' => ['Zs', 0, 0x20],
        ];
    }
}
