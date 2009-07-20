<?php
/**
 * Copyright (c) 2009 Stefan Priebsch <stefan@priebsch.de>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 *   * Redistributions of source code must retain the above copyright notice,
 *     this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright notice,
 *     this list of conditions and the following disclaimer in the documentation
 *     and/or other materials provided with the distribution.
 *
 *   * Neither the name of Stefan Priebsch nor the names of contributors
 *     may be used to endorse or promote products derived from this software
 *     without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER ORCONTRIBUTORS
 * BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
 * OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    PHPca
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 * @license    BSD License
 */

namespace spriebsch\PHPca;

require_once 'PHPUnit/Framework.php';
require_once __DIR__ . '/../src/Exceptions.php';
require_once __DIR__ . '/../src/Loader.php';

/**
 * Tests for the Tokenizer class.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
class TokenizerTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        Loader::init();
        Loader::registerPath(__DIR__ . '/../src');
    }

    protected function tearDown()
    {
        Loader::reset();
    }

    /**
     * @covers spriebsch\PHPca\Tokenizer::tokenize
     */
    public function testTokenizeReturnsFileObject()
    {
        $file = Tokenizer::tokenize('filename', "<?php print 'hello world'; ?>");
        $this->assertTrue($file instanceOf File);
    }

    /**
     * @covers spriebsch\PHPca\Tokenizer::tokenize
     */
    public function testTokenizeCountsLinesAndColumns()
    {
        $file = Tokenizer::tokenize('filename', "<?php \n\n function hello()\n{\n    print 'hello world';\n} \n ?>");

        $this->assertEquals('T_FUNCTION', $file[2]->getName());
        $this->assertEquals(3, $file[2]->getLine());
        $this->assertEquals(2, $file[2]->getColumn());

        $this->assertEquals('T_CLOSE_BRACKET', $file[6]->getName());
        $this->assertEquals(3, $file[6]->getLine());
        $this->assertEquals(17, $file[6]->getColumn());

        $this->assertEquals('T_OPEN_CURLY', $file[8]->getName());
        $this->assertEquals(4, $file[8]->getLine());
        $this->assertEquals(1, $file[8]->getColumn());
    }
}
?>