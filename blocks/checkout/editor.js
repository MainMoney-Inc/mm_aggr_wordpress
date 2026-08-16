(function (blocks, element, blockEditor, components) {
  const el = element.createElement;
  const { InspectorControls, useBlockProps } = blockEditor;
  const { PanelBody, TextControl } = components;

  blocks.registerBlockType("mm-aggr/checkout", {
    edit: function (props) {
      const amount = props.attributes.amount || "";
      const currency = props.attributes.currency || "";
      const reference = props.attributes.reference || "";
      return el(
        "div",
        useBlockProps(),
        el(
          InspectorControls,
          {},
          el(
            PanelBody,
            { title: "MainMoney" },
            el(TextControl, {
              label: "Amount",
              value: amount,
              onChange: function (value) {
                props.setAttributes({ amount: value });
              },
            }),
            el(TextControl, {
              label: "Currency",
              value: currency,
              onChange: function (value) {
                props.setAttributes({ currency: value });
              },
            }),
            el(TextControl, {
              label: "Reference",
              value: reference,
              onChange: function (value) {
                props.setAttributes({ reference: value });
              },
            })
          )
        ),
        el("p", {}, "MainMoney checkout")
      );
    },
    save: function () {
      return null;
    },
  });
})(window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components);
