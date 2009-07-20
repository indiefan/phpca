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

/**
 * Creates a File object holding a collection of Tokens
 * from given source code. Uses PHP's built-in tokenizer but
 * adds on some custom tokens
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
class Tokenizer
{
    /**
     * Tokenize a file
     *
     * @param string $fileName    the file name 
     * @param string $sourceCode  the source code
     * @return File
     */
    static public function tokenize($fileName, $sourceCode)
    {
        Constants::init();

        $line     = 1;
        $column   = 1;

        $file = new File($fileName, $sourceCode);

        foreach(token_get_all($sourceCode) as $token) {
            if (is_array($token)) {
                $id   = $token[0];
                $text = $token[1];
                $line = $token[2];
            } else {
            
                try {
                    // it's not a PHP token, so we use one we have defined
                    $id   = Constants::getTokenId($token);
                    $text = $token;
                }

                // @codeCoverageIgnoreStart
                catch (UnkownTokenException $e) {
                    throw new TokenizerException('Unknown token ' . $e->getTokenName() . ' in file ' . $fileName);
                }
                // This exception is not testable, because we _have_ defined all
                // tokens, hopefully. It's just a safeguard to provide a decent
                // error message should we ever encounter an undefined token.
                // @codeCoverageIgnoreEnd
            }

            $tokenObj = new Token($id, $text, $line, $column);

            if ($tokenObj->hasNewline()) {
                // a newline resets the column count
                $line  += $tokenObj->getNewLineCount();
                $column = 1 + $tokenObj->getTrailingWhitespaceCount();
            } else {
                $column += $tokenObj->getLength();
            }

            $file->add($tokenObj);
        }

        return $file;
    }
}
?>